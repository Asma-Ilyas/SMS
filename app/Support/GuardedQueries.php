<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Schema-tolerant query helpers for the student portal.
 *
 * Every dataset is described as: candidate table names + alias => candidate column names.
 * The first table / column that really exists is used; missing columns come back as NULL
 * so a slightly different schema never causes a 500 error.
 *
 * SECURITY: every query is scoped to the logged-in student. If the column that would
 * scope a query does not exist, the query returns NOTHING (fail closed) instead of
 * returning other students' data.
 */
trait GuardedQueries
{
    protected static array $guardTableCache = [];
    protected static array $guardColumnCache = [];

    protected function tableOf(array $candidates): ?string
    {
        foreach ($candidates as $table) {
            if (! array_key_exists($table, static::$guardTableCache)) {
                static::$guardTableCache[$table] = Schema::hasTable($table);
            }
            if (static::$guardTableCache[$table]) {
                return $table;
            }
        }

        return null;
    }

    protected function columnsOf(string $table): array
    {
        return static::$guardColumnCache[$table] ??= Schema::getColumnListing($table);
    }

    /**
     * @return array{0: \Illuminate\Database\Query\Builder, 1: array<string, ?string>}|null
     */
    protected function guarded(array $tables, array $columns, array $scope = []): ?array
    {
        $table = $this->tableOf($tables);
        if (! $table) {
            return null;
        }

        $existing = $this->columnsOf($table);
        $grammar  = DB::connection()->getQueryGrammar();
        $resolved = [];
        $select   = [];

        foreach ($columns as $alias => $candidates) {
            $found = null;
            foreach ((array) $candidates as $candidate) {
                if (in_array($candidate, $existing, true)) {
                    $found = $candidate;
                    break;
                }
            }

            $resolved[$alias] = $found ? $table . '.' . $found : null;
            $select[] = $found
                ? DB::raw($grammar->wrap($table . '.' . $found) . ' as ' . $grammar->wrap($alias))
                : DB::raw('NULL as ' . $grammar->wrap($alias));
        }

        $query = DB::table($table)->select($select);

        foreach ($scope as $alias => $value) {
            if (empty($resolved[$alias])) {
                return null; // fail closed
            }
            $query->where($resolved[$alias], $value);
        }

        return [$query, $resolved];
    }

    protected function fetch(
        array $tables,
        array $columns,
        array $scope = [],
        ?string $orderBy = null,
        string $direction = 'desc',
        ?callable $extra = null
    ): Collection {
        $built = $this->guarded($tables, $columns, $scope);
        if (! $built) {
            return collect();
        }

        [$query, $resolved] = $built;

        if ($extra) {
            $extra($query, $resolved);
        }
        if ($orderBy && ! empty($resolved[$orderBy])) {
            $query->orderBy($resolved[$orderBy], $direction);
        }

        return $query->get();
    }

    /** Fetch rows by primary key (used to resolve names such as subject / teacher / room). */
    protected function lookup(array $tables, array $columns, iterable $ids, string $keyAlias = 'id'): Collection
    {
        $ids = collect($ids)->filter(fn ($v) => $v !== null && $v !== '')->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        $built = $this->guarded($tables, $columns);
        if (! $built) {
            return collect();
        }

        [$query, $resolved] = $built;
        if (empty($resolved[$keyAlias])) {
            return collect();
        }

        return $query->whereIn($resolved[$keyAlias], $ids->all())->get()->keyBy($keyAlias);
    }

    /**
     * Restrict a query to the student: rows whose student column matches, OR rows that belong
     * to one of the student's own parent records ($via = [alias => ids]). No usable column => no rows.
     */
    protected function scopeToOwner($query, array $resolved, $studentId, array $via = []): void
    {
        $query->where(function ($w) use ($resolved, $studentId, $via) {
            $any = false;

            if (! empty($resolved['student_id'])) {
                $w->orWhere($resolved['student_id'], $studentId);
                $any = true;
            }

            foreach ($via as $alias => $ids) {
                $ids = collect($ids)->filter()->values();
                if (! empty($resolved[$alias]) && $ids->isNotEmpty()) {
                    $w->orWhereIn($resolved[$alias], $ids->all());
                    $any = true;
                }
            }

            if (! $any) {
                $w->whereRaw('1 = 0');
            }
        });
    }

    protected function parse($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function fmt($value, string $format = 'd M Y'): ?string
    {
        return $this->parse($value)?->format($format);
    }

    protected function isTruthy($value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'active', 'published', 'on'], true);
    }
}