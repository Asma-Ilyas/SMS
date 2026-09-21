<?php

namespace App\Services\Notifications;

use App\Models\Student;
use App\Support\GuardedQueries;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Works out which User accounts should receive a notification.
 * Student -> user: students.user_id when that column exists, otherwise the same e-mail address
 * (exactly how the student portal matches a login to a student record).
 */
class RecipientResolver
{
    use GuardedQueries;

    public function userModel(): string
    {
        return config('auth.providers.users.model', \App\Models\User::class);
    }

    public function byRole(string $role): Collection
    {
        $class = $this->userModel();

        return method_exists($class, 'scopeRole') ? $class::role($role)->get() : collect();
    }

    public function admins(): Collection
    {
        return $this->byRole('admin');
    }

    public function teachers(): Collection
    {
        return $this->byRole('teacher');
    }

    public function students(): Collection
    {
        return $this->byRole('student');
    }

    public function everyone(): Collection
    {
        return $this->admins()->concat($this->teachers())->concat($this->students())->unique(fn ($u) => $u->getKey())->values();
    }

    /** Student models in a class section, or (when only a class is known) in all sections of that class. */
    public function studentsForScope($sectionId, $classId = null): Collection
    {
        if ($sectionId) {
            return Student::whereHas('classSection', fn ($q) => $q->whereKey($sectionId))->get();
        }
        if ($classId) {
            return Student::whereHas('classSection', fn ($q) => $q->where('class_id', $classId))->get();
        }

        return collect();
    }

    public function usersForStudents(Collection $students): Collection
    {
        if ($students->isEmpty()) {
            return collect();
        }

        $class     = $this->userModel();
        $keyName   = (new $class)->getKeyName();
        $hasUserId = Schema::hasColumn((new Student)->getTable(), 'user_id');

        $ids    = $hasUserId ? $students->pluck('user_id')->filter()->unique() : collect();
        $emails = $students->pluck('email')->filter()->unique();

        return $class::query()->where(function ($q) use ($ids, $emails, $keyName) {
            $any = false;
            if ($ids->isNotEmpty()) {
                $q->orWhereIn($keyName, $ids->all());
                $any = true;
            }
            if ($emails->isNotEmpty()) {
                $q->orWhereIn('email', $emails->all());
                $any = true;
            }
            if (! $any) {
                $q->whereRaw('1 = 0');
            }
        })->get();
    }

    /** @return Collection student id => user */
    public function mapStudentsToUsers(Collection $students): Collection
    {
        $users     = $this->usersForStudents($students);
        $byId      = $users->keyBy(fn ($u) => (string) $u->getKey());
        $byEmail   = $users->filter(fn ($u) => ! empty($u->email))->keyBy(fn ($u) => strtolower($u->email));
        $hasUserId = Schema::hasColumn((new Student)->getTable(), 'user_id');

        return $students->mapWithKeys(function ($s) use ($byId, $byEmail, $hasUserId) {
            $user = null;
            if ($hasUserId && $s->user_id) {
                $user = $byId->get((string) $s->user_id);
            }
            if (! $user && ! empty($s->email)) {
                $user = $byEmail->get(strtolower($s->email));
            }

            return [$s->getKey() => $user];
        })->filter();
    }

    public function userForStudent(Student $student)
    {
        return $this->mapStudentsToUsers(collect([$student]))->first();
    }

    /** Staff / teacher record -> login account (staff.user_id, else the same e-mail). */
    public function userForStaff($staffId)
    {
        if (! $staffId) {
            return null;
        }

        $staff = $this->fetch(
            ['staff', 'staffs', 'teachers', 'employees'],
            ['id' => ['id'], 'user_id' => ['user_id'], 'email' => ['email']],
            ['id' => $staffId]
        )->first();

        if (! $staff) {
            return null;
        }

        $class = $this->userModel();

        if ($staff->user_id && ($user = $class::find($staff->user_id))) {
            return $user;
        }

        return $staff->email ? $class::where('email', $staff->email)->first() : null;
    }
}