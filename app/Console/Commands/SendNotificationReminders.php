<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\Notifications\PortalNotifier;
use App\Services\Notifications\RecipientResolver;
use App\Support\GuardedQueries;
use Illuminate\Console\Command;

/**
 * Time-based notifications (things that are not caused by someone saving a record):
 *   - fee installment due in the next N days / overdue        -> student
 *   - exam starting tomorrow                                   -> students of that class
 *   - leave requests / payment proofs waiting for approval    -> admins
 *
 * Every message carries a de-duplication key, so running this twice never sends the same reminder twice.
 * Schedule it daily (see INSTALL.md).
 */
class SendNotificationReminders extends Command
{
    use GuardedQueries;

    protected $signature   = 'portal:notify-reminders';
    protected $description = 'Send fee, exam and approval reminders to students and admins';

    private const PAID   = ['paid', 'approved', 'completed', 'settled', 'cancelled', 'waived'];
    private const REVIEW = ['pending_approval', 'submitted', 'under_review', 'verification', 'proof_uploaded', 'awaiting_approval', 'pending approval'];

    public function handle(PortalNotifier $notifier, RecipientResolver $recipients): int
    {
        [$soon, $overdue] = $this->feeReminders($notifier, $recipients);
        $exams            = $this->examReminders($notifier, $recipients);
        $admin            = $this->adminReminders($notifier, $recipients);

        $this->info("Fee due soon: {$soon} | Fee overdue: {$overdue} | Exam tomorrow: {$exams} | Admin reminders: {$admin}");

        return self::SUCCESS;
    }

    protected function feeReminders(PortalNotifier $notifier, RecipientResolver $recipients): array
    {
        $until = now()->addDays((int) config('portal-notifications.fee_due_days', 3))->toDateString();

        $rows = $this->fetch(
            ['fee_installments'],
            [
                'id'         => ['id'],
                'student_id' => ['student_id'],
                'title'      => ['title', 'name', 'installment_name', 'description'],
                'amount'     => ['amount', 'installment_amount', 'total_amount'],
                'discount'   => ['discount', 'discount_amount'],
                'fine'       => ['fine', 'fine_amount', 'late_fee'],
                'paid'       => ['paid_amount', 'amount_paid'],
                'status'     => ['status', 'payment_status'],
                'due'        => ['due_date'],
            ],
            [],
            null,
            'desc',
            function ($query, $c) use ($until) {
                if ($c['due'] && $c['student_id']) {
                    $query->whereDate($c['due'], '<=', $until);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        )->filter(function ($r) {
            if (in_array(strtolower((string) $r->status), self::PAID, true)) {
                return false;
            }
            if ($r->amount === null) {
                return true;
            }

            return ((float) $r->amount + (float) $r->fine - (float) $r->discount) - (float) $r->paid > 0;
        });

        if ($rows->isEmpty()) {
            return [0, 0];
        }

        $students = Student::whereIn((new Student)->getKeyName(), $rows->pluck('student_id')->unique()->all())->get();
        $users    = $recipients->mapStudentsToUsers($students);
        $url      = $notifier->path('student.fees.index');
        $soon     = 0;
        $overdue  = 0;

        foreach ($rows as $r) {
            $user = $users->get($r->student_id);
            $due  = $this->parse($r->due);
            if (! $user || ! $due) {
                continue;
            }

            $balance = max(((float) $r->amount + (float) $r->fine - (float) $r->discount) - (float) $r->paid, 0);
            $label   = ($r->title ?: 'Fee installment') . ($balance > 0 ? ' (Rs. ' . number_format($balance) . ')' : '');
            $dueText = $due->format('d M Y');

            if ($due->copy()->endOfDay()->isPast()) {
                $overdue += $notifier->send($user, 'Fee overdue', "{$label} was due on {$dueText}. Please pay as soon as possible.", [
                    'category' => 'fee', 'level' => 'danger', 'url' => $url,
                    'key' => "fee-overdue-{$r->id}-" . now()->format('o-W'), 'dedupe_days' => 6,
                ]);
            } else {
                $soon += $notifier->send($user, 'Fee due soon', "{$label} is due on {$dueText}.", [
                    'category' => 'fee', 'level' => 'warning', 'url' => $url,
                    'key' => "fee-due-{$r->id}", 'dedupe_days' => 30,
                ]);
            }
        }

        return [$soon, $overdue];
    }

    protected function examReminders(PortalNotifier $notifier, RecipientResolver $recipients): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $exams = $this->fetch(
            ['exams'],
            [
                'id'         => ['id'],
                'name'       => ['name', 'title', 'exam_name'],
                'class_id'   => ['class_id'],
                'section_id' => ['class_section_id'],
                'start'      => ['start_date', 'exam_date', 'date'],
            ],
            [],
            null,
            'desc',
            function ($query, $c) use ($tomorrow) {
                $c['start'] ? $query->whereDate($c['start'], $tomorrow) : $query->whereRaw('1 = 0');
            }
        );

        $sent = 0;
        foreach ($exams as $exam) {
            $users = $recipients->usersForStudents($recipients->studentsForScope($exam->section_id, $exam->class_id));

            $sent += $notifier->send($users, 'Exam tomorrow', ($exam->name ?: 'An exam') . ' starts tomorrow. Good luck!', [
                'category' => 'exam', 'level' => 'warning', 'url' => $notifier->path('student.exams.index'),
                'key' => "exam-tomorrow-{$exam->id}", 'dedupe_days' => 3,
            ]);
        }

        return $sent;
    }

    protected function adminReminders(PortalNotifier $notifier, RecipientResolver $recipients): int
    {
        $admins = $recipients->admins();
        $today  = now()->toDateString();
        $sent   = 0;

        $leaves = $this->countWithStatus(['leaves', 'leave_requests'], ['pending']);
        if ($leaves > 0) {
            $sent += $notifier->send($admins, 'Leave requests waiting', "{$leaves} leave " . ($leaves === 1 ? 'request is' : 'requests are') . ' waiting for your decision.', [
                'category' => 'leave', 'level' => 'info', 'url' => $notifier->path('admin.leaves.index'),
                'key' => "pending-leaves-{$today}", 'dedupe_days' => 1,
            ]);
        }

        $proofs = $this->countWithStatus(['fee_installments'], self::REVIEW);
        if ($proofs > 0) {
            $sent += $notifier->send($admins, 'Fee payments waiting for approval', "{$proofs} fee " . ($proofs === 1 ? 'payment is' : 'payments are') . ' waiting for approval.', [
                'category' => 'fee', 'level' => 'warning', 'url' => $notifier->path('admin.fee-installments.index'),
                'key' => "pending-payments-{$today}", 'dedupe_days' => 1,
            ]);
        }

        return $sent;
    }

    protected function countWithStatus(array $tables, array $statuses): int
    {
        return $this->fetch($tables, ['status' => ['status', 'payment_status']], [], null, 'desc', function ($query, $c) use ($statuses) {
            $c['status'] ? $query->whereIn($c['status'], $statuses) : $query->whereRaw('1 = 0');
        })->count();
    }
}