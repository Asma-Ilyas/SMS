<?php

namespace App\Services\Notifications;

use App\Models\Student;
use App\Support\GuardedQueries;
use Illuminate\Database\Eloquent\Model;

/**
 * Automatic notifications fired from Eloquent model events (registered in PortalNotificationServiceProvider).
 *
 * Model attributes are read defensively (first existing column wins), so a slightly different column
 * name simply means "no notification" instead of an error.
 */
class NotificationTriggers
{
    use GuardedQueries;

    protected const PAID   = ['paid', 'approved', 'completed', 'settled'];
    protected const REVIEW = ['pending_approval', 'submitted', 'under_review', 'verification', 'proof_uploaded', 'awaiting_approval', 'pending approval'];

    public function __construct(protected PortalNotifier $notifier, protected RecipientResolver $recipients)
    {
    }

    /* ---------- helpers ---------- */

    protected function attr(Model $model, array $keys)
    {
        $attributes = $model->getAttributes();
        foreach ($keys as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] !== null && $attributes[$key] !== '') {
                return $attributes[$key];
            }
        }

        return null;
    }

    protected function changed(Model $model, array $keys): bool
    {
        $attributes = $model->getAttributes();
        foreach ($keys as $key) {
            if (array_key_exists($key, $attributes) && $model->wasChanged($key)) {
                return true;
            }
        }

        return false;
    }

    protected function status(Model $model): string
    {
        return strtolower(trim((string) $this->attr($model, ['status', 'payment_status', 'attendance_status'])));
    }

    protected function student($id): ?Student
    {
        return $id ? Student::find($id) : null;
    }

    protected function studentName(?Student $student): string
    {
        if (! $student) {
            return 'A student';
        }

        $attributes = $student->getAttributes();

        return $attributes['name'] ?? $attributes['full_name']
            ?? trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')) ?: 'A student';
    }

    protected function money($value): string
    {
        return 'Rs. ' . number_format((float) $value);
    }

    protected function sectionLabel($sectionId): string
    {
        $class = 'App\\Models\\ClassSection';
        if (! $sectionId || ! class_exists($class)) {
            return 'a class';
        }

        $section = $class::with('class')->find($sectionId);
        if (! $section) {
            return 'a class';
        }

        $sectionAtt = $section->getAttributes();

        return trim(($section->class?->getAttributes()['name'] ?? '') . ' ' . ($sectionAtt['name'] ?? '')) ?: 'a class';
    }

    protected function notifyStudent(?Student $student, string $title, string $message, array $opts): void
    {
        if (! $student || ! ($user = $this->recipients->userForStudent($student))) {
            return;
        }

        $this->notifier->send($user, $title, $message, $opts);
    }

    protected function isPublished(Model $exam): bool
    {
        $flag = $this->attr($exam, ['is_published', 'published', 'result_published', 'results_published']);
        if ($flag !== null) {
            return $this->isTruthy($flag);
        }

        return in_array($this->status($exam), ['published', 'result_published', 'results_published'], true);
    }

    /* ---------- students: attendance ---------- */

    public function attendanceSaved(Model $record, bool $created): void
    {
        if (! $created && ! $this->changed($record, ['status', 'attendance_status'])) {
            return;
        }
        if (! in_array($this->status($record), ['absent', 'a', '0'], true)) {
            return;
        }

        $student = $this->student($this->attr($record, ['student_id']));
        $date    = $this->fmt($this->attr($record, ['date', 'attendance_date']));

        $this->notifyStudent(
            $student,
            'Marked absent',
            $date ? "You were marked absent on {$date}." : 'You were marked absent today.',
            [
                'category'    => 'attendance',
                'level'       => 'warning',
                'url'         => $this->notifier->path('student.attendance.index'),
                'key'         => 'absent-' . $student?->getKey() . '-' . ($date ?? now()->toDateString()),
                'dedupe_days' => 2,
            ]
        );
    }

    /* ---------- exams ---------- */

    public function examSaved(Model $exam, bool $created): void
    {
        $sectionId = $this->attr($exam, ['class_section_id']);
        $classId   = $this->attr($exam, ['class_id']);
        $name      = $this->attr($exam, ['name', 'title', 'exam_name']) ?? 'An exam';

        if ($created) {
            $start = $this->fmt($this->attr($exam, ['start_date', 'exam_date', 'date']));
            $users = $this->recipients->usersForStudents($this->recipients->studentsForScope($sectionId, $classId));

            $this->notifier->send($users, 'New exam scheduled', $start ? "{$name} starts on {$start}." : "{$name} has been scheduled.", [
                'category' => 'exam',
                'level'    => 'info',
                'url'      => $this->notifier->path('student.exams.index'),
            ]);

            return;
        }

        if ($this->changed($exam, ['is_published', 'published', 'result_published', 'results_published', 'status']) && $this->isPublished($exam)) {
            $users = $this->recipients->usersForStudents($this->recipients->studentsForScope($sectionId, $classId));

            $this->notifier->send($users, 'Results published', "Results for {$name} are now available.", [
                'category' => 'exam',
                'level'    => 'success',
                'url'      => $this->notifier->path('student.results.show', ['exam' => $exam->getKey()]),
                'key'      => 'results-published-' . $exam->getKey(),
                'dedupe_days' => 30,
            ]);
        }
    }

    /* ---------- leave requests (staff) ---------- */

    protected function leaveRequester(Model $leave)
    {
        $class  = $this->recipients->userModel();
        $userId = $this->attr($leave, ['user_id']);

        if ($userId && ($user = $class::find($userId))) {
            return $user;
        }

        return $this->recipients->userForStaff($this->attr($leave, ['staff_id', 'teacher_id', 'employee_id']));
    }

    public function leaveSaved(Model $leave, bool $created): void
    {
        $requester = $this->leaveRequester($leave);

        if ($created) {
            $from = $this->fmt($this->attr($leave, ['from_date', 'start_date', 'date_from']));
            $to   = $this->fmt($this->attr($leave, ['to_date', 'end_date', 'date_to']));
            $name = $requester?->name ?? 'A staff member';

            $admins = $this->recipients->admins()->reject(fn ($a) => $requester && $a->getKey() == $requester->getKey());

            $this->notifier->send($admins, 'New leave request', $name . ' requested leave' . ($from ? " from {$from}" . ($to && $to !== $from ? " to {$to}" : '') : '') . '.', [
                'category' => 'leave',
                'level'    => 'info',
                'url'      => $this->notifier->path('admin.leaves.index'),
            ]);

            return;
        }

        if (! $requester || ! $this->changed($leave, ['status'])) {
            return;
        }

        $status = $this->status($leave);
        if (! in_array($status, ['approved', 'rejected', 'declined'], true)) {
            return;
        }

        $approved = $status === 'approved';
        $route    = method_exists($requester, 'hasRole') && $requester->hasRole('teacher') ? 'teacher.leaves.index' : 'admin.leaves.my';

        $this->notifier->send($requester, $approved ? 'Leave request approved' : 'Leave request rejected', $approved
            ? 'Your leave request has been approved.'
            : 'Your leave request was not approved. Contact the administration for details.', [
            'category' => 'leave',
            'level'    => $approved ? 'success' : 'danger',
            'url'      => $this->notifier->path($route),
        ]);
    }

    /* ---------- salary ---------- */

    public function salarySaved(Model $salary, bool $created): void
    {
        if ($created || ! $this->changed($salary, ['status', 'payment_status'])) {
            return;
        }
        if (! in_array($this->status($salary), ['paid', 'completed'], true)) {
            return;
        }

        $class = $this->recipients->userModel();
        $user  = ($id = $this->attr($salary, ['user_id'])) ? $class::find($id) : null;
        $user ??= $this->recipients->userForStaff($this->attr($salary, ['staff_id', 'employee_id', 'teacher_id']));
        if (! $user) {
            return;
        }

        $month = $this->attr($salary, ['month', 'salary_month', 'for_month']);
        $label = $month ? ($this->fmt($month, 'F Y') ?? $month) : null;

        $this->notifier->send($user, 'Salary paid', $label ? "Your salary for {$label} has been paid." : 'Your salary has been paid.', [
            'category' => 'salary',
            'level'    => 'success',
            'url'      => method_exists($user, 'hasRole') && $user->hasRole('teacher') ? $this->notifier->path('teacher.salary.index') : null,
        ]);
    }

    /* ---------- fees ---------- */

    public function installmentSaved(Model $installment, bool $created): void
    {
        $student = $this->student($this->attr($installment, ['student_id']));

        if ($created) {
            $label  = $this->attr($installment, ['title', 'name', 'installment_name', 'description']) ?? 'Fee installment';
            $amount = $this->attr($installment, ['amount', 'installment_amount', 'total_amount']);
            $due    = $this->fmt($this->attr($installment, ['due_date']));

            $this->notifyStudent($student, 'New fee installment', $label . ($amount ? ' of ' . $this->money($amount) : '') . ($due ? " is due on {$due}." : ' has been added.'), [
                'category' => 'fee',
                'level'    => 'info',
                'url'      => $this->notifier->path('student.fees.index'),
                'key'      => 'installment-new-' . $installment->getKey(),
                'dedupe_days' => 30,
            ]);

            return;
        }

        if (! $this->changed($installment, ['status', 'payment_status'])) {
            return;
        }

        $status = $this->status($installment);

        if (in_array($status, self::REVIEW, true)) {
            $this->notifier->send($this->recipients->admins(), 'Payment proof submitted', $this->studentName($student) . ' submitted a fee payment for approval.', [
                'category' => 'fee',
                'level'    => 'warning',
                'url'      => $this->notifier->path('admin.fee-installments.index'),
            ]);
        } elseif (in_array($status, self::PAID, true)) {
            $this->notifyStudent($student, 'Payment received', 'Your fee payment has been received and approved. Thank you.', [
                'category' => 'fee',
                'level'    => 'success',
                'url'      => $this->notifier->path('student.fees.index'),
                'key'      => 'installment-paid-' . $installment->getKey(),
                'dedupe_days' => 30,
            ]);
        }
    }

    public function invoiceCreated(Model $invoice): void
    {
        $student = $this->student($this->attr($invoice, ['student_id']));
        $number  = $this->attr($invoice, ['invoice_number', 'invoice_no', 'challan_no', 'number']);
        $amount  = $this->attr($invoice, ['total_amount', 'amount', 'grand_total']);

        $this->notifyStudent($student, 'New fee invoice', 'Invoice ' . ($number ?? '#' . $invoice->getKey()) . ($amount ? ' for ' . $this->money($amount) : '') . ' has been generated.', [
            'category' => 'fee',
            'level'    => 'info',
            'url'      => $this->notifier->path('student.fees.invoices.show', ['invoice' => $invoice->getKey()]),
        ]);
    }

    /* ---------- teachers ---------- */

    public function subjectAssigned(Model $assignment): void
    {
        $user = $this->recipients->userForStaff($this->attr($assignment, ['teacher_id', 'staff_id', 'employee_id']));
        if (! $user) {
            return;
        }

        $subject = $this->lookup(['subjects'], ['id' => ['id'], 'name' => ['name', 'subject_name', 'title']], [$this->attr($assignment, ['subject_id'])])->first();

        $this->notifier->send(
            $user,
            'New subject assigned',
            'You have been assigned ' . ($subject->name ?? 'a subject') . ' for ' . $this->sectionLabel($this->attr($assignment, ['class_section_id'])) . '.',
            ['category' => 'general', 'level' => 'info', 'url' => $this->notifier->path('teacher.subjects.index')]
        );
    }

    /* ---------- admins ---------- */

    public function admissionCreated(Model $application): void
    {
        $attributes = $application->getAttributes();
        $name       = $this->attr($application, ['name', 'student_name', 'full_name'])
            ?? trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? ''));

        $this->notifier->send($this->recipients->admins(), 'New admission application', ($name ?: 'A new applicant') . ' has applied for admission.', [
            'category' => 'admission',
            'level'    => 'info',
        ]);
    }

    /* ---------- student services ---------- */

    public function certificateIssued(Model $certificate): void
    {
        $this->notifyStudent($this->student($this->attr($certificate, ['student_id'])), 'Certificate issued', 'A new certificate has been issued to you.', [
            'category' => 'certificate',
            'level'    => 'success',
            'url'      => $this->notifier->path('student.certificates.index'),
        ]);
    }

    public function transferCertificateIssued(Model $certificate): void
    {
        $this->notifyStudent($this->student($this->attr($certificate, ['student_id'])), 'Transfer certificate issued', 'Your transfer certificate has been issued.', [
            'category' => 'certificate',
            'level'    => 'info',
            'url'      => $this->notifier->path('student.certificates.index'),
        ]);
    }

    public function hostelAllocated(Model $allocation): void
    {
        $this->notifyStudent($this->student($this->attr($allocation, ['student_id'])), 'Hostel room allocated', 'A hostel room has been allocated to you.', [
            'category' => 'hostel',
            'level'    => 'info',
            'url'      => $this->notifier->path('student.hostel.index'),
        ]);
    }

    public function transportAssigned(Model $assignment): void
    {
        $this->notifyStudent($this->student($this->attr($assignment, ['student_id'])), 'School transport assigned', 'You have been assigned a school transport route.', [
            'category' => 'transport',
            'level'    => 'info',
            'url'      => $this->notifier->path('student.transport.index'),
        ]);
    }

    public function studentUpdated(Student $student): void
    {
        if ($this->changed($student, ['status'])) {
            $status = $this->status($student);
            $bad    = in_array($status, ['suspended', 'inactive', 'left', 'expelled'], true);

            $this->notifyStudent($student, 'Account status updated', 'Your account status is now ' . ($status !== '' ? ucfirst($status) : 'updated') . '.', [
                'category' => 'account',
                'level'    => $bad ? 'danger' : 'success',
                'url'      => $this->notifier->path('student.dashboard'),
            ]);
        }

        if ($this->changed($student, ['class_section_id'])) {
            $this->notifyStudent($student, 'Class updated', 'You have been moved to ' . $this->sectionLabel($this->attr($student, ['class_section_id'])) . '.', [
                'category' => 'account',
                'level'    => 'info',
                'url'      => $this->notifier->path('student.dashboard'),
            ]);
        }
    }
}