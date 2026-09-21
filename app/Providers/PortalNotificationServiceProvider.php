<?php

namespace App\Providers;

use App\Services\Notifications\NotificationTriggers;
use Illuminate\Support\ServiceProvider;

/**
 * Hooks the automatic notification triggers onto your Eloquent models.
 * A model that does not exist in your project is silently skipped, and a trigger that throws
 * is reported but never breaks the action the user was performing.
 *
 * Note: Eloquent events do not fire for bulk queries such as Model::where(...)->update([...]) or DB::table()->update().
 */
class PortalNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/portal-notifications.php', 'portal-notifications');
    }

    public function boot(): void
    {
        $M = 'App\\Models\\';

        $this->hook('attendance_absent', [$M . 'StudentAttendance'], [
            'created' => fn ($t, $m) => $t->attendanceSaved($m, true),
            'updated' => fn ($t, $m) => $t->attendanceSaved($m, false),
        ]);

        $this->hook('exam', [$M . 'Exam'], [
            'created' => fn ($t, $m) => $t->examSaved($m, true),
            'updated' => fn ($t, $m) => $t->examSaved($m, false),
        ]);

        $this->hook('leave', [$M . 'Leave', $M . 'LeaveRequest'], [
            'created' => fn ($t, $m) => $t->leaveSaved($m, true),
            'updated' => fn ($t, $m) => $t->leaveSaved($m, false),
        ]);

        $this->hook('salary', [$M . 'Salary', $M . 'StaffSalary', $M . 'Payroll'], [
            'updated' => fn ($t, $m) => $t->salarySaved($m, false),
        ]);

        $this->hook('fee_installment', [$M . 'FeeInstallment'], [
            'created' => fn ($t, $m) => $t->installmentSaved($m, true),
            'updated' => fn ($t, $m) => $t->installmentSaved($m, false),
        ]);

        $this->hook('invoice', [$M . 'Invoice'], [
            'created' => fn ($t, $m) => $t->invoiceCreated($m),
        ]);

        $this->hook('subject_assignment', [$M . 'SubjectAssignment'], [
            'created' => fn ($t, $m) => $t->subjectAssigned($m),
        ]);

        $this->hook('admission', [$M . 'Admission', $M . 'AdmissionApplication', $M . 'AdmissionRequest'], [
            'created' => fn ($t, $m) => $t->admissionCreated($m),
        ]);

        $this->hook('certificate', [$M . 'CertificateDistribution'], [
            'created' => fn ($t, $m) => $t->certificateIssued($m),
        ]);

        $this->hook('transfer_certificate', [$M . 'TransferCertificate'], [
            'created' => fn ($t, $m) => $t->transferCertificateIssued($m),
        ]);

        $this->hook('hostel', [$M . 'HostelAllocation'], [
            'created' => fn ($t, $m) => $t->hostelAllocated($m),
        ]);

        $this->hook('transport', [$M . 'StudentTransport'], [
            'created' => fn ($t, $m) => $t->transportAssigned($m),
        ]);

        $this->hook('student', [$M . 'Student'], [
            'updated' => fn ($t, $m) => $t->studentUpdated($m),
        ]);
    }

    /** @param string[] $classes first class that exists wins */
    protected function hook(string $trigger, array $classes, array $events): void
    {
        if (! config("portal-notifications.triggers.$trigger", true)) {
            return;
        }

        foreach ($classes as $class) {
            if (! class_exists($class)) {
                continue;
            }

            foreach ($events as $event => $handler) {
                $class::$event(function ($model) use ($handler) {
                    if (app()->runningInConsole() && ! config('portal-notifications.notify_in_console')) {
                        return;
                    }

                    try {
                        $handler(app(NotificationTriggers::class), $model);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                });
            }

            return;
        }
    }
}