<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\ClassesController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FeeSubmissionController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\FeeInstallmentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\StudentDiscountController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\TransferCertificateController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\StudentFeeSubmissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SubjectAssignmentController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AttendanceCheckController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\EmployeeCategoryController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\ExamGroupController;
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\TimeTableController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\ExamMarkController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\GradeScaleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/welcome/{schoolSlug}/{pageSlug}', [PageController::class, 'dynamicPage']);
Route::post('/admission/apply', [AdmissionController::class, 'store'])->name('admission.apply');

/*
|--------------------------------------------------------------------------
| Admin Routes – all prefixed with /admin and named 'admin.'
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Core Resources
    Route::resource('sessions', SessionController::class);
    Route::resource('classes', ClassesController::class);
    Route::resource('grades', GradeController::class);
    Route::resource('streams', StreamController::class);
    Route::resource('students', StudentController::class);
    Route::get('get-sections/{class_id}', [StudentController::class, 'getSectionsByClass']);
    Route::get('get-students/{class_id}/{section}', [StudentController::class, 'getStudentsByClassSection']);

    // Fees & Installments
    Route::resource('fee-submissions', FeeSubmissionController::class);
    Route::get('/fee-types/{feeType}/amount', [FeeSubmissionController::class, 'getFeeTypeAmount']);
    Route::resource('fee-types', FeeTypeController::class);
    Route::resource('fee-installments', FeeInstallmentController::class);
    Route::get('fee-installments/{installment}/pay', [FeeInstallmentController::class, 'payForm'])->name('fee-installments.pay-form');
    Route::post('fee-installments/{installment}/pay', [FeeInstallmentController::class, 'pay'])->name('fee-installments.pay');
    Route::post('fee-installments/{installment}/generate-invoice', [FeeInstallmentController::class, 'generateInvoice'])->name('fee-installments.generate-invoice');
    Route::get('fee-installments/{installment}/download-challan', [FeeInstallmentController::class, 'downloadChallan'])->name('fee-installments.download-challan');
    Route::post('fee-installments/{installment}/upload-proof', [FeeInstallmentController::class, 'uploadProof'])->name('fee-installments.upload-proof');
    Route::post('fee-installments/{installment}/approve', [FeeInstallmentController::class, 'approvePayment'])->name('fee-installments.approve');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download-challan', [InvoiceController::class, 'downloadChallan'])->name('invoices.download-challan');
    Route::post('invoices/{invoice}/upload-proof', [InvoiceController::class, 'uploadPaymentProof'])->name('invoices.upload-proof');
    Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approvePayment'])->name('invoices.approve');
    Route::get('/get-sections/{classId}', [InvoiceController::class, 'getSections']);
    Route::get('/get-students/{sectionId}', [InvoiceController::class, 'getStudents']);
    Route::get('/get-installments/{studentId}', [InvoiceController::class, 'getInstallmentsByStudent']);
    Route::get('/get-students-by-class/{classId}', [InvoiceController::class, 'getStudentsByClass']);

    // Banks & Discounts
    Route::resource('banks', BankController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('discount-assignments', StudentDiscountController::class);

    // Student Promotion
    Route::post('students/{student}/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::post('classes/{class}/promote-all', [ClassesController::class, 'promoteAll'])->name('classes.promote-all');

    // Transfers & Certificates
    Route::resource('transfers', TransferController::class)->only(['index']);
    Route::get('students/{student}/transfer-out', [TransferController::class, 'transferOutForm'])->name('students.transfer-out.form');
    Route::post('students/{student}/transfer-out', [TransferController::class, 'transferOut'])->name('students.transfer-out');
    Route::get('transfer-in', [TransferController::class, 'transferInForm'])->name('transfers.incoming.form');
    Route::post('transfer-in', [TransferController::class, 'transferInStore'])->name('transfers.incoming.store');

    Route::get('transfer-certificates', [TransferCertificateController::class, 'index'])->name('transfer-certificates.index');
    Route::post('transfer-certificates', [TransferCertificateController::class, 'store'])->name('transfer-certificates.store');
    Route::get('sections/{classId}', [TransferCertificateController::class, 'getSections']);
    Route::get('students/{classId}/{section}', [TransferCertificateController::class, 'getStudents']);
    Route::get('student-data/{studentId}', [TransferCertificateController::class, 'getStudentData']);

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateController::class, 'createType'])->name('certificates.create-type');
    Route::post('certificates', [CertificateController::class, 'storeType'])->name('certificates.store-type');
    Route::get('certificates/{certificateType}/edit', [CertificateController::class, 'editType'])->name('certificates.edit-type');
    Route::put('certificates/{certificateType}', [CertificateController::class, 'updateType'])->name('certificates.update-type');
    Route::delete('certificates/{certificateType}', [CertificateController::class, 'destroyType'])->name('certificates.destroy-type');
    Route::get('certificates/{certificateType}/distribute', [CertificateController::class, 'distributeForm'])->name('certificates.distribute');
    Route::post('certificates/{certificateType}/distribute', [CertificateController::class, 'distribute'])->name('certificates.distribute.store');
    Route::get('certificates-history', [CertificateController::class, 'history'])->name('certificates.history');
    Route::get('certificates/download/{distribution}', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('sections/{classId}', [CertificateController::class, 'getSections']);
    Route::get('students/{classId}/{section}', [CertificateController::class, 'getStudents']);
    Route::get('student-info/{studentId}', [CertificateController::class, 'getStudentInfo']);

    // Fee Reports
    Route::resource('fee-submissions', StudentFeeSubmissionController::class);
    Route::get('/fee-reports', [ReportController::class, 'index'])->name('fee-reports.index');
    Route::get('/fee-reports/student/{student}', [ReportController::class, 'studentDetails'])->name('fee-reports.student');

    // Staff
    Route::resource('staff', StaffController::class);

    // Subjects & Assignments
    Route::resource('subjects', SubjectController::class);
    Route::prefix('subject-assignments')->name('subject-assignments.')->group(function () {
        Route::get('/', [SubjectAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [SubjectAssignmentController::class, 'create'])->name('create');
        Route::post('/', [SubjectAssignmentController::class, 'store'])->name('store');
        Route::delete('/{id}', [SubjectAssignmentController::class, 'destroy'])->name('destroy');
    });

    // Staff Attendance (check-in/out)
    Route::get('attendance/check', [AttendanceCheckController::class, 'showCheckinForm'])->name('attendance.check');
    Route::post('attendance/checkin', [AttendanceCheckController::class, 'checkin'])->name('attendance.checkin');
    Route::post('attendance/checkout', [AttendanceCheckController::class, 'checkout'])->name('attendance.checkout');
    Route::resource('attendance', AttendanceController::class);

    // Salary
    Route::resource('salaries', SalaryController::class);
    Route::post('salaries/generate-payroll', [SalaryController::class, 'generatePayroll'])->name('salaries.generate-payroll');
    Route::get('salaries/export', [SalaryController::class, 'export'])->name('salaries.export');
    Route::patch('/salaries/{salary}/mark-paid', [SalaryController::class, 'markPaidUpdate'])->name('salaries.mark-paid.update');
    Route::get('/salaries/{salary}/mark-paid', [SalaryController::class, 'markPaidForm'])->name('salaries.mark-paid');

    // Leave
    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('leaves/request', [LeaveController::class, 'requestLeave'])->name('leaves.request');
    Route::patch('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('my-leaves', [LeaveController::class, 'myLeaves'])->name('leaves.my');

    // Employee Categories
    Route::resource('employee-categories', EmployeeCategoryController::class);

    // ========== EXAMINATION MODULE ==========
    // Exam Groups & Types
    Route::resource('exam-groups', ExamGroupController::class);
    Route::resource('exam-types', ExamTypeController::class);

    // Exams (resource + custom routes for marks entry & publishing)
    Route::resource('exams', ExamController::class)->except(['show']);
    Route::get('exams/{exam}/marks-entry', [ExamController::class, 'marksEntryForm'])->name('exams.marks-entry');
    Route::post('exams/{exam}/marks', [ExamController::class, 'storeMarks'])->name('exams.store-marks');
    Route::get('exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
    Route::get('exams/{exam}/unpublish', [ExamController::class, 'unpublish'])->name('exams.unpublish');
    Route::get('exams/{exam}/result/{student}', [ExamController::class, 'showResult'])->name('exams.result');

    // Exam Results & Reports (using ExamResultController)
    Route::prefix('exam-results')->name('exam-results.')->group(function () {
        Route::get('{exam}/bulk-print', [ExamResultController::class, 'bulkPrintForm'])->name('bulk-print-form');
        Route::post('{exam}/bulk-print', [ExamResultController::class, 'bulkPrint'])->name('bulk-print');
        Route::get('academic-report/{student}', [ExamResultController::class, 'academicReport'])->name('academic-report');
        Route::get('multi-group-report-form', [ExamResultController::class, 'multiGroupReportForm'])->name('multi-group-report.form');
        Route::post('multi-group-report', [ExamResultController::class, 'multiGroupReport'])->name('multi-group-report');
    });

    // Additional ExamResult routes (clean URLs)
    Route::get('/exams/{exam}/marks-entry', [ExamResultController::class, 'marksEntryForm'])->name('exams.marks-entry');
    Route::post('/exams/{exam}/marks-entry', [ExamResultController::class, 'storeMarks'])->name('exams.marks-entry.store');
    Route::get('/exams/{exam}/class-section-marksheet/{class?}/{section?}', [ExamResultController::class, 'classSectionMarksheet'])
        ->name('pdf.class-section-marksheet');

    // ========== STUDENT ATTENDANCE ==========
    Route::get('/studentattendance', [StudentAttendanceController::class, 'index'])->name('studentattendance.index');
    Route::get('/studentattendance/create', [StudentAttendanceController::class, 'create'])->name('studentattendance.create');
    Route::post('/studentattendance/store', [StudentAttendanceController::class, 'store'])->name('studentattendance.store');
    Route::get('/studentattendance/report', [StudentAttendanceController::class, 'report'])->name('studentattendance.report');
    Route::get('/studentattendance/student/{student_id}', [StudentAttendanceController::class, 'studentReport'])->name('studentattendance.student');

    // ========== TIME TABLE ==========
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', [TimeTableController::class, 'index'])->name('index');
        Route::get('/create', [TimeTableController::class, 'create'])->name('create');
        Route::post('/', [TimeTableController::class, 'store'])->name('store');
        Route::get('/{timetable}/edit', [TimeTableController::class, 'edit'])->name('edit');
        Route::put('/{timetable}', [TimeTableController::class, 'update'])->name('update');
        Route::delete('/{timetable}', [TimeTableController::class, 'destroy'])->name('destroy');
    });

    // ========== SECTION-WISE SUBJECTS ==========
    Route::resource('class-subject', ClassSubjectController::class);

    // ========== EXAM MARKS (NEW) ==========
    Route::prefix('exam-marks')->name('exam-marks.')->group(function () {
        Route::get('/', [ExamMarkController::class, 'index'])->name('index');
        Route::get('/create', [ExamMarkController::class, 'create'])->name('create');
        Route::post('/store', [ExamMarkController::class, 'store'])->name('store');
        Route::get('/bulk-upload', [ExamMarkController::class, 'bulkUploadForm'])->name('bulk-upload');
        Route::post('/bulk-upload', [ExamMarkController::class, 'bulkUploadStore'])->name('bulk-upload.store');
    });

    // ========== RESULTS (NEW) ==========
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/class-wise', [ResultController::class, 'classWise'])->name('class-wise');
        Route::get('/student-wise', [ResultController::class, 'studentWise'])->name('student-wise');
        Route::get('/department-wise', [ResultController::class, 'departmentWise'])->name('department-wise');
        Route::get('/pdf-report/{studentId}/{examId?}', [ResultController::class, 'pdfReportCard'])->name('pdf-report');
        Route::post('/recalc/{examId}', [ResultController::class, 'recalcExam'])->name('recalc');
    });

    // ========== GRADE SCALES ==========
    Route::resource('grade-scales', GradeScaleController::class);

}); // <-- CLOSES THE ADMIN GROUP – DO NOT MOVE OR DELETE

/*
|--------------------------------------------------------------------------
| Wildcard Route – MUST BE LAST (catch‑all)
|--------------------------------------------------------------------------
*/
Route::get('/{schoolSlug}/{pageSlug}', [PageController::class, 'dynamicPage'])->name('page.show');