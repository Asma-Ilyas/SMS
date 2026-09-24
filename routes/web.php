<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\ClassesController;
use App\Http\Controllers\Admin\ClassSectionController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FeeSubmissionController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\FeeInstallmentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ExamResultAnalysisController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\StudentDiscountController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\TransferCertificateController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentReportCardController;
use App\Http\Controllers\Admin\TeacherExamReportController;
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
use App\Http\Controllers\Admin\SchoolTimingController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\TimetableReportController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\BlockController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\TeacherAvailabilityController;
use App\Http\Controllers\Admin\ExamReportController;
use App\Http\Controllers\Admin\StudentReportController;
use App\Http\Controllers\Admin\TeacherAttendanceController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\TransportRouteController;
use App\Http\Controllers\Admin\StudentTransportController;
use App\Http\Controllers\Admin\TransportFeeController;
use App\Http\Controllers\Admin\VehicleTripLogController;
use App\Http\Controllers\Admin\HostelController;
use App\Http\Controllers\Admin\HostelStaffController;
use App\Http\Controllers\Admin\HostelRoomController;
use App\Http\Controllers\Admin\HostelRoomTypeController;
use App\Http\Controllers\Admin\HostelAllocationController;
use App\Http\Controllers\Admin\HostelFeeController;
use App\Http\Controllers\Admin\TransportDashboardController;
use App\Http\Controllers\Admin\HostelDashboardController;
use App\Http\Controllers\Admin\TransportHostelAlertController;

// ============================================================
// NOTIFICATIONS + ANNOUNCEMENTS CONTROLLERS
// ============================================================
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AnnouncementController;

// ============================================================
// TEACHER CONTROLLERS — Safe Aliases (Tp prefix = teacher portal)
// ============================================================
use App\Http\Controllers\Teacher\DashboardController         as TpDashboardController;
use App\Http\Controllers\Teacher\ProfileController           as TpProfileController;
use App\Http\Controllers\Teacher\SchoolTimingController      as TpSchoolTimingController;
use App\Http\Controllers\Teacher\SubjectController           as TpSubjectController;
use App\Http\Controllers\Teacher\TimetableController         as TpTimetableController;
use App\Http\Controllers\Teacher\StudentController           as TpStudentController;
use App\Http\Controllers\Teacher\AttendanceController        as TpAttendanceController;
use App\Http\Controllers\Teacher\AttendanceHistoryController as TpAttendanceHistoryController;
use App\Http\Controllers\Teacher\ExamController              as TpExamController;
use App\Http\Controllers\Teacher\MarksController             as TpMarksController;
use App\Http\Controllers\Teacher\ExamReportController        as TpExamReportController;
use App\Http\Controllers\Teacher\ReportController            as TpReportController;
use App\Http\Controllers\Teacher\ReportCardController        as TpReportCardController;
use App\Http\Controllers\Teacher\TimetableReportController   as TpTimetableReportController;
use App\Http\Controllers\Teacher\SalaryController            as TpSalaryController;
use App\Http\Controllers\Teacher\LeaveController             as TpLeaveController;

// ============================================================
// STUDENT CONTROLLERS — Safe Aliases (Portal*)
// ============================================================
use App\Http\Controllers\Student\DashboardController   as PortalDashboardController;
use App\Http\Controllers\Student\ProfileController     as PortalProfileController;
use App\Http\Controllers\Student\AttendanceController  as PortalAttendanceController;
use App\Http\Controllers\Student\TimetableController   as PortalTimetableController;
use App\Http\Controllers\Student\SubjectController     as PortalSubjectController;
use App\Http\Controllers\Student\ExamController        as PortalExamController;
use App\Http\Controllers\Student\ResultController      as PortalResultController;
use App\Http\Controllers\Student\FeeController         as PortalFeeController;
use App\Http\Controllers\Student\TransportController   as PortalTransportController;
use App\Http\Controllers\Student\HostelController      as PortalHostelController;
use App\Http\Controllers\Student\CertificateController as PortalCertificateController;

// ============================================================
// 1. AUTHENTICATION ROUTES (Public)
// ============================================================
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================
// 2. ROOT — Redirect based on role
// ============================================================
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    $user = auth()->user();

    if ($user->hasRole('admin'))   return redirect('/admin/dashboard');
    if ($user->hasRole('teacher')) return redirect('/teacher/dashboard');
    if ($user->hasRole('student')) return redirect('/student/dashboard');

    return redirect('/login');
});

// ============================================================
// 3. PUBLIC ROUTES
// ============================================================
Route::view('/landing', 'landing')->name('landing'); 
Route::get('/welcome/{schoolSlug}/{pageSlug}', [PageController::class, 'dynamicPage']);
Route::post('/admission/apply', [AdmissionController::class, 'store'])->name('admission.apply');

// ============================================================
// NOTIFICATIONS (all logged-in roles)
// ============================================================
Route::middleware('auth')
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/feed', [NotificationController::class, 'feed'])->name('feed');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
        Route::get('/{id}/open', [NotificationController::class, 'open'])->name('open');
        Route::post('/{id}/read', [NotificationController::class, 'read'])->name('read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

// ============================================================
// 4. ADMIN ROUTES (auth + role:admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ============================================================
    // ACADEMIC CORE
    // ============================================================
    Route::resource('sessions', SessionController::class);
    Route::resource('classes', ClassesController::class);
    Route::resource('grades', GradeController::class);
    Route::resource('streams', StreamController::class);
    Route::resource('class-sections', ClassSectionController::class)->except('show');
    Route::post('/class-sections/{classSection}/promote-all', [ClassesController::class, 'promoteAll'])->name('class-sections.promote-all');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/get-students-by-class-section/{classSectionId}', [StudentController::class, 'getStudentsByClassSection'])->name('students.by-class-section');
    Route::post('/students/{student}/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::post('/students/{student}/suspend', [StudentController::class, 'suspend'])->name('students.suspend');
    Route::post('/students/{student}/reactivate', [StudentController::class, 'reactivate'])->name('students.reactivate');
    Route::get('/students/{student}/attendance', [StudentController::class, 'attendanceReport'])->name('students.attendance');
    Route::get('/students/{student}/exam-results', [StudentController::class, 'examResults'])->name('students.exam-results');
    Route::get('/students/{student}/fee-details', [StudentController::class, 'feeDetails'])->name('students.fee-details');

    // ============================================================
    // FEES & INVOICES
    // ============================================================
    Route::resource('fee-submissions', FeeSubmissionController::class);
    Route::get('/fee-types/{feeType}/amount', [FeeSubmissionController::class, 'getFeeTypeAmount']);
    Route::resource('fee-types', FeeTypeController::class);

    Route::resource('fee-installments', FeeInstallmentController::class);
    Route::get('fee-installments/{installment}/pay', [FeeInstallmentController::class, 'payForm'])->name('fee-installments.pay-form');
    Route::post('fee-installments/{installment}/pay', [FeeInstallmentController::class, 'pay'])->name('fee-installments.pay');
    Route::post('fee-installments/{installment}/generate-invoice', [FeeInstallmentController::class, 'generateInvoiceForInstallment'])
    ->name('fee-installments.generate-invoice');
    // REMOVED: generate-invoice (see below)
    Route::get('fee-installments/{installment}/download-challan', [FeeInstallmentController::class, 'downloadChallan'])->name('fee-installments.download-challan');
    Route::post('fee-installments/{installment}/upload-proof', [FeeInstallmentController::class, 'uploadProof'])->name('fee-installments.upload-proof');
    Route::post('fee-installments/{installment}/approve', [FeeInstallmentController::class, 'approvePayment'])->name('fee-installments.approve');
    

    // CHANGED: only the methods that exist in InvoiceController
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{invoice}/download-challan', [InvoiceController::class, 'downloadChallan'])->name('invoices.download-challan');
    Route::post('invoices/{invoice}/upload-proof', [InvoiceController::class, 'uploadPaymentProof'])->name('invoices.upload-proof');
    Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approvePayment'])->name('invoices.approve');
    Route::post('invoices/{invoice}/reject', [InvoiceController::class, 'rejectPayment'])->name('invoices.reject');   // ADDED
    Route::get('/get-installments/{studentId}', [InvoiceController::class, 'getInstallmentsByStudent'])->name('invoices.installments');
    Route::get('/get-students-by-class-section/{classSectionId}', [InvoiceController::class, 'getStudentsByClassSection'])->name('invoices.students.by-class-section');

    Route::resource('banks', BankController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('discount-assignments', StudentDiscountController::class);


    // ============================================================
    // TRANSFERS & CERTIFICATES
    // ============================================================
    Route::prefix('transfer-certificates')->name('transfer-certificates.')->group(function () {
        Route::get('/', [TransferCertificateController::class, 'index'])->name('index');
        Route::get('/create', [TransferCertificateController::class, 'create'])->name('create');
        Route::post('/', [TransferCertificateController::class, 'store'])->name('store');
        Route::get('/{transferCertificate}', [TransferCertificateController::class, 'show'])->name('show');
        Route::get('/{transferCertificate}/edit', [TransferCertificateController::class, 'edit'])->name('edit');
        Route::put('/{transferCertificate}', [TransferCertificateController::class, 'update'])->name('update');
        Route::delete('/{transferCertificate}', [TransferCertificateController::class, 'destroy'])->name('destroy');
        Route::get('/get-sections/{classId}', [TransferCertificateController::class, 'getSections'])->name('get-sections');
        Route::get('/get-students/{sectionId}', [TransferCertificateController::class, 'getStudents'])->name('get-students');
        Route::get('/get-student-data/{studentId}', [TransferCertificateController::class, 'getStudentData'])->name('get-student-data');
        Route::get('/download/{id}', [TransferCertificateController::class, 'download'])->name('download');
    });

    // Certificates
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
    Route::get('students-by-class-section/{classSectionId}', [CertificateController::class, 'getStudentsBySection'])
    ->name('certificates.students.by-class-section');
    Route::get('student-info/{studentId}', [CertificateController::class, 'getStudentInfo'])->name('certificates.student-info');

    // ============================================================
    // FEE REPORTS
    // ============================================================
    Route::get('/fee-reports', [ReportController::class, 'index'])->name('fee-reports.index');
    Route::get('/fee-reports/student/{student}', [ReportController::class, 'studentDetails'])->name('fee-reports.student');

    // ============================================================
    // TRANSPORT MODULE
    // ============================================================
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/maintenance', [VehicleController::class, 'storeMaintenance'])->name('vehicles.maintenance.store');

    Route::resource('transport-routes', TransportRouteController::class);
    Route::post('transport-routes/{transportRoute}/stops', [TransportRouteController::class, 'addStop'])->name('transport-routes.stops.add');
    Route::delete('transport-routes/{transportRoute}/stops/{stopId}', [TransportRouteController::class, 'removeStop'])->name('transport-routes.stops.remove');

    Route::resource('student-transports', StudentTransportController::class);
    Route::get('routes/{routeId}/stops', [StudentTransportController::class, 'getStopsByRoute'])->name('transport-routes.stops.by-route');

    Route::prefix('transport-fee-types')->name('transport-fee-types.')->group(function () {
        Route::get('/', [TransportFeeController::class, 'index'])->name('index');
        Route::get('/create', [TransportFeeController::class, 'create'])->name('create');
        Route::post('/', [TransportFeeController::class, 'store'])->name('store');
        Route::get('/{transportFeeType}/edit', [TransportFeeController::class, 'edit'])->name('edit');
        Route::put('/{transportFeeType}', [TransportFeeController::class, 'update'])->name('update');
        Route::delete('/{transportFeeType}', [TransportFeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('transport-fee-payments')->name('transport-fee-payments.')->group(function () {
        Route::get('/', [TransportFeeController::class, 'payments'])->name('index');
        Route::post('/generate', [TransportFeeController::class, 'generateMonthly'])->name('generate');
        Route::post('/{payment}/pay', [TransportFeeController::class, 'pay'])->name('pay');
    });

    Route::prefix('vehicle-trips')->name('vehicle-trips.')->group(function () {
        Route::get('/', [VehicleTripLogController::class, 'index'])->name('index');
        Route::get('/create', [VehicleTripLogController::class, 'create'])->name('create');
        Route::post('/', [VehicleTripLogController::class, 'store'])->name('store');
        Route::post('/{vehicleTrip}/status', [VehicleTripLogController::class, 'updateStatus'])->name('status');
        Route::delete('/{vehicleTrip}', [VehicleTripLogController::class, 'destroy'])->name('destroy');
    });

    // ============================================================
    // HOSTEL MODULE
    // ============================================================
    Route::resource('hostels', HostelController::class);
    Route::resource('hostel-staff', HostelStaffController::class)->except(['show', 'edit', 'update']);
    Route::resource('hostel-room-types', HostelRoomTypeController::class)->except(['show', 'create', 'edit']);
    Route::resource('hostel-rooms', HostelRoomController::class);

    Route::resource('hostel-allocations', HostelAllocationController::class)->except(['destroy']);
    Route::delete('hostel-allocations/{hostelAllocation}', [HostelAllocationController::class, 'destroy'])->name('hostel-allocations.destroy');
    Route::post('hostel-allocations/{hostelAllocation}/vacate', [HostelAllocationController::class, 'vacate'])->name('hostel-allocations.vacate');
    Route::get('hostels/{hostelId}/rooms', [HostelAllocationController::class, 'getRoomsByHostel'])->name('hostel-allocations.rooms-by-hostel');

    Route::prefix('hostel-fee-types')->name('hostel-fee-types.')->group(function () {
        Route::get('/', [HostelFeeController::class, 'index'])->name('index');
        Route::get('/create', [HostelFeeController::class, 'create'])->name('create');
        Route::post('/', [HostelFeeController::class, 'store'])->name('store');
        Route::get('/{hostelFeeType}/edit', [HostelFeeController::class, 'edit'])->name('edit');
        Route::put('/{hostelFeeType}', [HostelFeeController::class, 'update'])->name('update');
        Route::delete('/{hostelFeeType}', [HostelFeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('hostel-fee-payments')->name('hostel-fee-payments.')->group(function () {
        Route::get('/', [HostelFeeController::class, 'payments'])->name('index');
        Route::post('/generate', [HostelFeeController::class, 'generateMonthly'])->name('generate');
        Route::post('/{payment}/pay', [HostelFeeController::class, 'pay'])->name('pay');
    });

    // ============================================================
    // DASHBOARDS & REPORTS
    // ============================================================
    Route::get('transport-dashboard', [TransportDashboardController::class, 'index'])->name('transport-dashboard.index');
    Route::get('hostel-dashboard', [HostelDashboardController::class, 'index'])->name('hostel-dashboard.index');

    // ============================================================
    // ALERTS
    // ============================================================
    Route::get('transport-hostel-alerts', [TransportHostelAlertController::class, 'index'])->name('transport-hostel-alerts.index');
    Route::get('transport-hostel-alerts/summary', [TransportHostelAlertController::class, 'summary'])->name('transport-hostel-alerts.summary');

    // ============================================================
    // STAFF & HR
    // ============================================================
    Route::resource('staff', StaffController::class);
    Route::resource('employee-categories', EmployeeCategoryController::class);

    Route::get('attendance/check', [AttendanceCheckController::class, 'showCheckinForm'])->name('attendance.check');
    Route::post('attendance/checkin', [AttendanceCheckController::class, 'checkin'])->name('attendance.checkin');
    Route::post('attendance/checkout', [AttendanceCheckController::class, 'checkout'])->name('attendance.checkout');
    Route::resource('attendance', AttendanceController::class);

    // ============================================================
    // ANNOUNCEMENTS
    // ============================================================
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'create', 'store', 'destroy']);

    // ============================================================
    // SALARY MANAGEMENT
    // ============================================================
   Route::prefix('salaries')->name('salaries.')->group(function () {
    // Static/literal paths FIRST
    Route::get('/', [SalaryController::class, 'index'])->name('index');
    Route::post('/generate-payroll', [SalaryController::class, 'generatePayroll'])->name('generate-payroll');
    Route::post('/calculate/{staffId}', [SalaryController::class, 'calculateIndividual'])->name('calculate');
    Route::get('/preview/{staffId}', [SalaryController::class, 'preview'])->name('preview');
    Route::get('/export', [SalaryController::class, 'export'])->name('export');

    Route::get('/templates', [SalaryController::class, 'templates'])->name('templates');
    Route::get('/templates/create', [SalaryController::class, 'createTemplate'])->name('templates.create');
    Route::post('/templates', [SalaryController::class, 'storeTemplate'])->name('templates.store');
    Route::get('/templates/{id}/edit', [SalaryController::class, 'editTemplate'])->name('templates.edit');
    Route::put('/templates/{id}', [SalaryController::class, 'updateTemplate'])->name('templates.update');
    Route::delete('/templates/{id}', [SalaryController::class, 'destroyTemplate'])->name('templates.destroy');
    Route::post('/templates/apply', [SalaryController::class, 'applyTemplate'])->name('templates.apply');

    // Wildcard routes LAST
    Route::get('/{id}', [SalaryController::class, 'show'])->name('show');
    Route::get('/{id}/mark-paid', [SalaryController::class, 'markPaidForm'])->name('mark-paid');
    Route::post('/{id}/mark-paid', [SalaryController::class, 'markPaidUpdate'])->name('mark-paid.update');
});

    // ============================================================
    // LEAVES
    // ============================================================
    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('leaves/request', [LeaveController::class, 'requestLeave'])->name('leaves.request');
    Route::patch('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('my-leaves', [LeaveController::class, 'myLeaves'])->name('leaves.my');

    // ============================================================
    // TEACHER AVAILABILITY
    // ============================================================
    Route::prefix('teacher-availability')->name('teacher-availability.')->group(function () {
        Route::get('/', [TeacherAvailabilityController::class, 'index'])->name('index');
        Route::get('/create', [TeacherAvailabilityController::class, 'create'])->name('create');
        Route::post('/', [TeacherAvailabilityController::class, 'store'])->name('store');
        Route::delete('/{id}', [TeacherAvailabilityController::class, 'destroy'])->name('destroy');
        Route::get('/{teacher}/edit', [TeacherAvailabilityController::class, 'edit'])->name('edit');
    });

    // ============================================================
    // CURRICULUM
    // ============================================================
    Route::resource('subjects', SubjectController::class);

    Route::prefix('subject-assignments')->name('subject-assignments.')->group(function () {
        Route::get('/', [SubjectAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [SubjectAssignmentController::class, 'create'])->name('create');
        Route::post('/', [SubjectAssignmentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SubjectAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubjectAssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectAssignmentController::class, 'destroy'])->name('destroy');
        Route::get('/bulk', [SubjectAssignmentController::class, 'bulkAssignForm'])->name('bulk');
        Route::post('/bulk', [SubjectAssignmentController::class, 'bulkAssign'])->name('bulk.store');
        Route::get('/get-sections/{classId}', [SubjectAssignmentController::class, 'getSections'])->name('get.sections');
        Route::get('/get-subjects/{sectionId}', [SubjectAssignmentController::class, 'getSubjectsBySection'])->name('get.subjects');
        Route::get('/get-assigned/{sectionId}', [SubjectAssignmentController::class, 'getAssignedSubjects'])->name('get-assigned');
    });

    Route::resource('class-subject', ClassSubjectController::class);

    // ============================================================
    // STUDENT ATTENDANCE
    // ============================================================
    Route::prefix('studentattendance')->name('studentattendance.')->group(function () {
        Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
        Route::get('/create', [StudentAttendanceController::class, 'create'])->name('create');
        Route::post('/store', [StudentAttendanceController::class, 'store'])->name('store');
        Route::get('/report', [StudentAttendanceController::class, 'report'])->name('report');
        Route::get('/export', [StudentAttendanceController::class, 'export'])->name('export');
        Route::get('/section-wise-report', [StudentAttendanceController::class, 'sectionWiseReport'])->name('section-wise-report');
        Route::get('/reasons/{category}', [StudentAttendanceController::class, 'getReasons'])->name('reasons');
        Route::get('/student/{student_id}', [StudentAttendanceController::class, 'studentReport'])->name('student');
        Route::get('/summary/{studentId}', [StudentAttendanceController::class, 'studentReport'])->name('summary');
        Route::post('/approve-leave/{id}', [StudentAttendanceController::class, 'approveLeave'])->name('approve-leave');
        Route::post('/reject-leave/{id}', [StudentAttendanceController::class, 'rejectLeave'])->name('reject-leave');
        Route::get('/pending-leaves', [StudentAttendanceController::class, 'pendingLeaveRequests'])->name('pending-leaves');
        Route::get('/permissions', [StudentAttendanceController::class, 'managePermissions'])->name('permissions');
        Route::post('/permissions', [StudentAttendanceController::class, 'storePermission'])->name('permissions.store');
        Route::delete('/permissions/{id}', [StudentAttendanceController::class, 'destroyPermission'])->name('permissions.destroy');
        Route::post('/permissions/bulk', [StudentAttendanceController::class, 'bulkAssignPermissions'])->name('permissions.bulk');
        Route::delete('/permissions/bulk', [StudentAttendanceController::class, 'bulkRemovePermissions'])->name('permissions.bulk-remove');
        Route::get('/permissions/teacher/{teacherId}', [StudentAttendanceController::class, 'viewTeacherPermissions'])->name('permissions.teacher');
        Route::get('/common-classes', [StudentAttendanceController::class, 'manageCommonClasses'])->name('common-classes');
        Route::get('/common-classes/edit/{id}', [StudentAttendanceController::class, 'editCommonClass'])->name('common-classes.edit');
        Route::put('/common-classes/{id}', [StudentAttendanceController::class, 'updateCommonClass'])->name('common-classes.update');
        Route::post('/common-classes', [StudentAttendanceController::class, 'storeCommonClass'])->name('common-classes.store');
        Route::delete('/common-classes/{id}', [StudentAttendanceController::class, 'destroyCommonClass'])->name('common-classes.destroy');
        Route::get('/{id}', [StudentAttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StudentAttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StudentAttendanceController::class, 'update'])->name('update');
        Route::delete('/{id}', [StudentAttendanceController::class, 'destroy'])->name('destroy');
    });

    // ============================================================
    // TEACHER ATTENDANCE
    // ============================================================
    Route::prefix('teacher-attendance')->name('teacher-attendance.')->group(function () {
        Route::get('/', [TeacherAttendanceController::class, 'index'])->name('index');
        Route::post('/mark', [TeacherAttendanceController::class, 'markAttendance'])->name('mark');
        Route::get('/class-wise', [TeacherAttendanceController::class, 'classWiseReport'])->name('class-wise');
        Route::get('/per-class', [TeacherAttendanceController::class, 'perClassAttendance'])->name('per-class');
        Route::get('/arrival-departure', [TeacherAttendanceController::class, 'arrivalDepartureReport'])->name('arrival-departure');
        Route::get('/summary', [TeacherAttendanceController::class, 'summaryReport'])->name('summary');
    });

    // ============================================================
    // STUDENT REPORTS
    // ============================================================
    Route::prefix('reports/student')->name('reports.student.')->group(function () {
        Route::get('/dashboard', [StudentReportController::class, 'index'])->name('dashboard');
        Route::get('/generate', [StudentReportController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [StudentReportController::class, 'exportPDF'])->name('export-pdf');
    });

    // ============================================================
    // STUDENT REPORT CARD
    // ============================================================
    Route::prefix('student-report-card')->name('student-report-card.')->group(function () {
        Route::get('/', [StudentReportCardController::class, 'index'])->name('index');
        Route::post('/generate', [StudentReportCardController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [StudentReportCardController::class, 'exportPDF'])->name('export-pdf');
    });

    // ============================================================
    // TIMETABLE REPORTS
    // ============================================================
    Route::prefix('timetable-reports')->name('timetable-reports.')->group(function () {
        Route::get('/', [TimetableReportController::class, 'index'])->name('index');
        Route::get('/class', [TimetableReportController::class, 'byClass'])->name('class');
        Route::get('/section', [TimetableReportController::class, 'bySection'])->name('section');
        Route::get('/by-section/{classSectionId}', [TimetableReportController::class, 'bySection'])->name('by-section');
        Route::get('/teacher', [TimetableReportController::class, 'byTeacher'])->name('teacher');
        Route::get('/print', [TimetableReportController::class, 'print'])->name('print');
        Route::get('/export', [TimetableReportController::class, 'export'])->name('export');
        Route::post('/generate', [TimetableReportController::class, 'generate'])->name('generate');
        Route::get('/auto-generate', [TimetableReportController::class, 'showAutoGenerate'])->name('auto-generate');
        Route::get('/logs', [TimetableReportController::class, 'logs'])->name('logs');
        Route::get('/no-active', [TimetableReportController::class, 'noActive'])->name('no-active');
        Route::get('/edit-entry/{id}', [TimetableReportController::class, 'editData'])->name('edit-entry');
        Route::put('/update-entry/{id}', [TimetableReportController::class, 'updateEntry'])->name('update-entry');
        Route::get('/edit-section/{classSectionId}', [TimetableReportController::class, 'editSectionTimetable'])->name('edit-section');
        Route::post('/update-section/{classSectionId}', [TimetableReportController::class, 'updateSectionTimetable'])->name('update-section');
    });

    // ============================================================
    // SCHOOL TIMINGS
    // ============================================================
    Route::prefix('school-timings')->name('school-timings.')->group(function () {
        Route::get('/', [SchoolTimingController::class, 'index'])->name('index');
        Route::get('/create', [SchoolTimingController::class, 'create'])->name('create');
        Route::post('/', [SchoolTimingController::class, 'store'])->name('store');
        Route::get('/{schoolTiming}', [SchoolTimingController::class, 'show'])->name('show');
        Route::get('/{schoolTiming}/edit', [SchoolTimingController::class, 'edit'])->name('edit');
        Route::put('/{schoolTiming}', [SchoolTimingController::class, 'update'])->name('update');
        Route::delete('/{schoolTiming}', [SchoolTimingController::class, 'destroy'])->name('destroy');
        Route::post('/{schoolTiming}/activate', [SchoolTimingController::class, 'activate'])->name('activate');
        Route::post('/{schoolTiming}/regenerate', [SchoolTimingController::class, 'regenerate'])->name('regenerate');
        Route::post('/{schoolTiming}/duplicate', [SchoolTimingController::class, 'duplicate'])->name('duplicate');
    });

    // ============================================================
    // TIME SLOTS
    // ============================================================
    Route::prefix('time-slots')->name('time-slots.')->group(function () {
        Route::get('/{schoolTiming}', [TimeSlotController::class, 'index'])->name('index');
        Route::get('/{schoolTiming}/create', [TimeSlotController::class, 'create'])->name('create');
        Route::post('/{schoolTiming}', [TimeSlotController::class, 'store'])->name('store');
        Route::get('/{schoolTiming}/{timeSlot}/edit', [TimeSlotController::class, 'edit'])->name('edit');
        Route::put('/{schoolTiming}/{timeSlot}', [TimeSlotController::class, 'update'])->name('update');
        Route::delete('/{schoolTiming}/{timeSlot}', [TimeSlotController::class, 'destroy'])->name('destroy');
        Route::post('/{schoolTiming}/reorder', [TimeSlotController::class, 'reorder'])->name('reorder');
        Route::post('/{timeSlot}/toggle', [TimeSlotController::class, 'toggle'])->name('toggle');
    });

    // ============================================================
    // EXAMINATION MODULE
    // ============================================================
    Route::resource('exam-types', ExamTypeController::class);
    Route::resource('exam-groups', ExamGroupController::class);
    Route::resource('grade-scales', GradeScaleController::class);

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
        Route::get('/{exam}/marks-entry', [ExamController::class, 'marksEntryForm'])->name('marks-entry');
        Route::post('/{exam}/marks', [ExamController::class, 'storeMarks'])->name('store-marks');
        Route::get('/{exam}/calculate-results', [ExamController::class, 'calculateResults'])->name('calculate-results');
        Route::get('/{exam}/publish', [ExamController::class, 'publish'])->name('publish');
        Route::get('/{exam}/unpublish', [ExamController::class, 'unpublish'])->name('unpublish');
        Route::get('/{exam}/result/{student}', [ExamController::class, 'showResult'])->name('result');
        Route::get('/{exam}/print-result/{student}', [ExamController::class, 'printResult'])->name('print-result');
    });

    Route::prefix('exam-results')->name('exam-results.')->group(function () {
        Route::get('/', [ExamResultAnalysisController::class, 'index'])->name('index');
        Route::get('/student-wise', [ExamResultAnalysisController::class, 'studentWise'])->name('student-wise');
        Route::get('/subject-wise', [ExamResultAnalysisController::class, 'subjectWise'])->name('subject-wise');
        Route::get('/section-wise', [ExamResultAnalysisController::class, 'sectionWise'])->name('section-wise');
        Route::get('/compare-tests', [ExamResultAnalysisController::class, 'compareTests'])->name('compare-tests');
        Route::get('/export', [ExamResultAnalysisController::class, 'export'])->name('export');
        Route::get('{exam}/bulk-print', [ExamResultController::class, 'bulkPrintForm'])->name('bulk-print-form');
        Route::post('{exam}/bulk-print', [ExamResultController::class, 'bulkPrint'])->name('bulk-print');
        Route::get('academic-report/{student}', [ExamResultController::class, 'academicReport'])->name('academic-report');
        Route::get('multi-group-report-form', [ExamResultController::class, 'multiGroupReportForm'])->name('multi-group-report.form');
        Route::post('multi-group-report', [ExamResultController::class, 'multiGroupReport'])->name('multi-group-report');
        Route::get('class-section-marksheet/{exam}/{classSectionId}', [ExamResultController::class, 'classSectionMarksheet'])->name('class-section-marksheet');
    });

    Route::get('/exams/{exam}/marks-entry', [ExamResultController::class, 'marksEntryForm'])->name('exams.marks-entry');
    Route::post('/exams/{exam}/marks-entry', [ExamResultController::class, 'storeMarks'])->name('exams.marks-entry.store');
    Route::get('/exams/{exam}/class-section-marksheet/{classSectionId}', [ExamResultController::class, 'classSectionMarksheet'])->name('pdf.class-section-marksheet');

    Route::prefix('exam-marks')->name('exam-marks.')->group(function () {
        Route::get('/', [ExamMarkController::class, 'index'])->name('index');
        Route::get('/create', [ExamMarkController::class, 'create'])->name('create');
        Route::post('/store', [ExamMarkController::class, 'store'])->name('store');
        Route::post('/save', [ExamMarkController::class, 'saveMarks'])->name('save');
        Route::get('/bulk-upload', [ExamMarkController::class, 'bulkUploadForm'])->name('bulk-upload');
        Route::post('/bulk-upload', [ExamMarkController::class, 'bulkUploadStore'])->name('bulk-upload.store');
    });

    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/class-wise', [ResultController::class, 'classWise'])->name('class-wise');
        Route::get('/student-wise', [ResultController::class, 'studentWise'])->name('student-wise');
        Route::get('/department-wise', [ResultController::class, 'departmentWise'])->name('department-wise');
        Route::get('/pdf-report/{studentId}/{examId?}', [ResultController::class, 'pdfReportCard'])->name('pdf-report');
        Route::post('/recalc/{examId}', [ResultController::class, 'recalcExam'])->name('recalc');
    });

    // ============================================================
    // EXAM REPORTS
    // ============================================================
    Route::prefix('exam-reports')->name('exams.reports.')->group(function () {
        Route::get('/', [ExamReportController::class, 'index'])->name('index');
        Route::get('/class-wise-form', [ExamReportController::class, 'classWiseForm'])->name('class-wise-form');
        Route::get('/class-wise', [ExamReportController::class, 'classWiseReport'])->name('class-wise');
        Route::get('/student-wise-form', [ExamReportController::class, 'studentWiseForm'])->name('student-wise-form');
        Route::get('/student-wise', [ExamReportController::class, 'studentWiseReport'])->name('student-wise');
        Route::get('/subject-wise-form', [ExamReportController::class, 'subjectWiseForm'])->name('subject-wise-form');
        Route::get('/subject-wise', [ExamReportController::class, 'subjectWiseReport'])->name('subject-wise');
        Route::get('/performance-analysis', [ExamReportController::class, 'performanceAnalysis'])->name('performance-analysis');
        Route::get('/grade-distribution-form', [ExamReportController::class, 'gradeDistributionForm'])->name('grade-distribution-form');
        Route::get('/grade-distribution', [ExamReportController::class, 'gradeDistribution'])->name('grade-distribution');
        Route::get('/export', [ExamReportController::class, 'export'])->name('export');
        Route::get('/teacher-wise', [TeacherExamReportController::class, 'index'])->name('teacher-wise');
        Route::get('/teacher-subjects', [TeacherExamReportController::class, 'getTeacherSubjects'])->name('teacher-subjects');
        Route::get('/teacher-subject-exams', [TeacherExamReportController::class, 'getTeacherSubjectExams'])->name('teacher-subject-exams');
        Route::get('/api/results', [TeacherExamReportController::class, 'apiGetResults'])->name('api.results');
        Route::get('/teacher-wise/export', [TeacherExamReportController::class, 'export'])->name('teacher-wise.export');
    });

    // ============================================================
    // INFRASTRUCTURE
    // ============================================================
    Route::resource('blocks', BlockController::class);
    Route::resource('floors', FloorController::class);

    Route::get('/rooms/assignments', [RoomController::class, 'assignments'])->name('rooms.assignments');
    Route::post('/rooms/auto-assign', [RoomController::class, 'autoAssign'])->name('rooms.auto-assign');
    Route::post('/rooms/manual-assign', [RoomController::class, 'manualAssign'])->name('rooms.manual-assign');
    Route::post('/rooms/adjust', [RoomController::class, 'adjustAssignment'])->name('rooms.adjust');
    Route::delete('/rooms/assignments/remove/{assignment}', [RoomController::class, 'removeAssignment'])->name('rooms.assignments.remove');
    Route::post('/rooms/{room}/toggle-availability', [RoomController::class, 'toggleAvailability'])->name('rooms.toggle-availability');
    Route::resource('rooms', RoomController::class);
});

// ============================================================
// 5. TEACHER ROUTES (auth + role:teacher)
// ============================================================
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // Core
        Route::get('/dashboard', [TpDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [TpProfileController::class, 'show'])->name('profile');
        Route::put('/profile/password', [TpProfileController::class, 'updatePassword'])->name('profile.password');

        // My teaching
        Route::get('/school-timings', [TpSchoolTimingController::class, 'index'])->name('school-timings.index');
        Route::get('/subjects',       [TpSubjectController::class, 'index'])->name('subjects.index');
        Route::get('/timetable',      [TpTimetableController::class, 'index'])->name('timetable');

        // My students
        Route::get('/students',             [TpStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}',   [TpStudentController::class, 'show'])->name('students.show');
        Route::get('/reports/students',     [TpReportController::class, 'students'])->name('reports.students');
        Route::get('/report-card',                    [TpReportCardController::class, 'index'])->name('report-card.index');
        Route::get('/report-card/{exam}/{student}',   [TpReportCardController::class, 'show'])->name('report-card.show');

        // Student attendance (marked by teacher)
        Route::get('/attendance',         [TpAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create',  [TpAttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance/store',  [TpAttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/report',  [TpAttendanceController::class, 'report'])->name('attendance.report');

        // My own attendance
        Route::get('/my-attendance',           [TpAttendanceHistoryController::class, 'index'])->name('attendance.history');
        Route::get('/my-attendance/check',     [TpAttendanceHistoryController::class, 'check'])->name('attendance.check');
        Route::post('/my-attendance/checkin',  [TpAttendanceHistoryController::class, 'checkin'])->name('attendance.checkin');
        Route::post('/my-attendance/checkout', [TpAttendanceHistoryController::class, 'checkout'])->name('attendance.checkout');

        // Exams & marks
        Route::get('/exams',           [TpExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}',    [TpExamController::class, 'show'])->name('exams.show');
        Route::get('/marks',           [TpMarksController::class, 'index'])->name('marks.index');
        Route::get('/marks/create',    [TpMarksController::class, 'create'])->name('marks.create');
        Route::post('/marks/store',    [TpMarksController::class, 'store'])->name('marks.store');
        Route::get('/exam-reports',    [TpExamReportController::class, 'index'])->name('exam-reports.index');

        // Timetable reports (read-only, school-wide)
        Route::get('/timetable-reports',          [TpTimetableReportController::class, 'index'])->name('timetable-reports.index');
        Route::get('/timetable-reports/class',    [TpTimetableReportController::class, 'byClass'])->name('timetable-reports.class');
        Route::get('/timetable-reports/section',  [TpTimetableReportController::class, 'bySection'])->name('timetable-reports.section');
        Route::get('/timetable-reports/teacher',  [TpTimetableReportController::class, 'byTeacher'])->name('timetable-reports.teacher');

        // My reports
        Route::get('/reports', [TpReportController::class, 'index'])->name('reports.index');

        // Salary
        Route::get('/salary',       [TpSalaryController::class, 'index'])->name('salary.index');
        Route::get('/salary/{id}',  [TpSalaryController::class, 'show'])->name('salary.show');

        // Leaves
        Route::get('/leaves',           [TpLeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create',    [TpLeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leaves/store',    [TpLeaveController::class, 'store'])->name('leaves.store');
        Route::delete('/leaves/{id}',   [TpLeaveController::class, 'destroy'])->name('leaves.destroy');
    });

// ============================================================
// 6. STUDENT ROUTES (auth + role:student)
// ============================================================
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [PortalDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [PortalProfileController::class, 'show'])->name('profile');
        Route::put('/profile/password', [PortalProfileController::class, 'updatePassword'])->name('profile.password');

        // Academics
        Route::get('/attendance', [PortalAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/timetable',  [PortalTimetableController::class, 'index'])->name('timetable.index');
        Route::get('/subjects',   [PortalSubjectController::class, 'index'])->name('subjects.index');

        // Exams & results
        Route::get('/exams',          [PortalExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}',   [PortalExamController::class, 'show'])->name('exams.show');
        Route::get('/results',        [PortalResultController::class, 'index'])->name('results.index');
        Route::get('/results/{exam}', [PortalResultController::class, 'show'])->name('results.show');

        // Fees
        Route::get('/fees',                            [PortalFeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/invoices/{invoice}',         [PortalFeeController::class, 'showInvoice'])->name('fees.invoice');
        Route::get('/fees/invoices/{invoice}/challan', [PortalFeeController::class, 'downloadChallan'])->name('fees.challan');
        Route::post('/fees/invoices/{invoice}/proof',  [PortalFeeController::class, 'uploadProof'])->name('fees.proof');

        // Facilities
        Route::get('/transport', [PortalTransportController::class, 'index'])->name('transport.index');
        Route::get('/hostel',    [PortalHostelController::class, 'index'])->name('hostel.index');

        // Certificates
        Route::get('/certificates',                         [PortalCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{distribution}/download', [PortalCertificateController::class, 'download'])->name('certificates.download');
    });

// ============================================================
// 7. AJAX ROUTES
// ============================================================
Route::get('/get-subjects-by-section/{sectionId}', [App\Http\Controllers\Admin\SubjectAssignmentController::class, 'getSubjectsBySection'])->name('get.subjects.by.section');
Route::get('/get-sections/{classId}', [App\Http\Controllers\Admin\SubjectAssignmentController::class, 'getSections'])->name('get.sections');

// ============================================================
// 8. WILDCARD ROUTE — MUST BE LAST
// ============================================================
Route::get('/{schoolSlug}/{pageSlug}', [PageController::class, 'dynamicPage'])->name('page.show');