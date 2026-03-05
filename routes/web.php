<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\ResultsController;
use App\Http\Controllers\Admin\CertificatesController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WhatsAppLogsController;
use App\Http\Controllers\Student\StudentController as StudentPortalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Public\StudentVerificationController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/privacy', function () {
    return view('public.privacy-policy');
})->name('privacy');

Route::get('/terms', function () {
    return view('public.terms-of-service');
})->name('terms');

Route::get('/data-deletion', function () {
    return view('public.data-deletion');
})->name('data-deletion');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', [RegisterController::class, 'success'])->name('register.success');

// Public Student Verification - Search page at /verify
Route::get('/verify', [StudentVerificationController::class, 'index'])->name('verify.index');
Route::post('/verify', [StudentVerificationController::class, 'search'])->name('verify.search');
Route::get('/verify/result/{student}', [StudentVerificationController::class, 'showResult'])->name('verify.result');

// Public Verify by Enrollment Number (QR code target)
Route::get('/verify/{enrollment_no}', [StudentVerificationController::class, 'verifyByEnrollment'])->name('verify.show');
Route::get('/verify/{enrollment_no}/photo', [StudentVerificationController::class, 'verifyPhoto'])->name('verify.photo');

// Backward compatibility - old URLs
Route::redirect('/public/student-verification', '/verify', 301);
Route::post('/public/student-verification/search', [StudentVerificationController::class, 'search'])->name('public.student-verification.search');

// Admin routes (Admin & Reception)
Route::middleware(['auth', 'role:admin,reception'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    
            // Students management
            Route::get('/students', [StudentController::class, 'index'])->name('students.index');
            Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
            Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::post('/students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
            Route::patch('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
            Route::patch('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');
            Route::post('/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
            Route::patch('/enrollments/{enrollment}/drop', [StudentController::class, 'dropEnrollment'])->name('enrollments.drop');
            Route::delete('/students/{student}/force-delete', [StudentController::class, 'forceDestroy'])->name('students.force-delete');
            Route::post('/students/{student}/force-delete', [StudentController::class, 'forceDestroy'])->name('students.force-delete.post');
            Route::delete('/enrollments/{enrollment}/remove', [StudentController::class, 'removeFromEnrollment'])->name('enrollments.remove');
            Route::get('/api/student-batches/by-course', [StudentController::class, 'getBatchesByCourse'])->name('students.batches-by-course');
            Route::get('/api/course-details/{courseId}', [StudentController::class, 'getCourseDetails'])->name('students.course-details');
            
            // Student Document Management
            Route::post('/students/{student}/documents', [StudentController::class, 'uploadDocument'])->name('students.upload-document');
            Route::put('/students/{student}/documents/{document}', [StudentController::class, 'updateDocument'])->name('students.update-document');
            Route::delete('/students/{student}/documents/{document}', [StudentController::class, 'removeDocument'])->name('students.remove-document');
    Route::get('/documents/{document}/file', [StudentController::class, 'viewDocument'])->name('documents.view');
            Route::get('/students/{student}/id-card/preview', [StudentController::class, 'idCardPreview'])->name('students.id-card.preview');
            Route::get('/students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
            Route::get('/students/{student}/id-card/download', [StudentController::class, 'downloadIdCard'])->name('students.id-card.download');
    
            // Payments management
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/pending', [PaymentController::class, 'pending'])->name('payments.pending');
            Route::get('/payments/debug', [PaymentController::class, 'debug'])->name('payments.debug');
            Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
            Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
            Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
            Route::patch('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
            Route::post('/payments/bulk-approve', [PaymentController::class, 'bulkApprove'])->name('payments.bulk-approve');
            Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
            Route::get('/payments/{payment}/receipt/pdf', [PaymentController::class, 'downloadReceiptPdf'])->name('payments.receipt.pdf');
            Route::get('/payments/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('payments.receipt');
            
            // API Routes for AJAX requests
            Route::get('/api/students', [PaymentController::class, 'getStudents'])->name('api.students');
            Route::get('/api/students/{student}/enrollments', [PaymentController::class, 'getStudentEnrollments'])->name('api.student.enrollments');
    
            // Courses management
            Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
            Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
            Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
            Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
            Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
            Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
            Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
            Route::patch('/courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    
    // Batches management
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
    Route::get('/batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
    Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])->name('batches.edit');
    Route::put('/batches/{batch}', [BatchController::class, 'update'])->name('batches.update');
    Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->name('batches.destroy');
    Route::patch('/batches/{batch}/toggle-status', [BatchController::class, 'toggleStatus'])->name('batches.toggle-status');
    Route::get('/api/batches/by-course', [BatchController::class, 'getBatchesByCourse'])->name('batches.by-course');
    
    // Assessments management
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
    Route::patch('/assessments/{assessment}/toggle-status', [AssessmentController::class, 'toggleStatus'])->name('assessments.toggle-status');
    Route::get('/api/assessments/batches/by-course', [AssessmentController::class, 'getBatchesByCourse'])->name('assessments.batches-by-course');
    
    // Question Bank management
    Route::get('/question-banks', [QuestionBankController::class, 'index'])->name('question-banks.index');
    Route::get('/question-banks/create', [QuestionBankController::class, 'create'])->name('question-banks.create');
    Route::post('/question-banks', [QuestionBankController::class, 'store'])->name('question-banks.store');
    Route::get('/question-banks/export', [QuestionBankController::class, 'export'])->name('question-banks.export');
    Route::get('/question-banks/{questionBank}', [QuestionBankController::class, 'show'])
        ->whereNumber('questionBank')
        ->name('question-banks.show');
    Route::get('/question-banks/{questionBank}/edit', [QuestionBankController::class, 'edit'])
        ->whereNumber('questionBank')
        ->name('question-banks.edit');
    Route::put('/question-banks/{questionBank}', [QuestionBankController::class, 'update'])
        ->whereNumber('questionBank')
        ->name('question-banks.update');
    Route::delete('/question-banks/{questionBank}', [QuestionBankController::class, 'destroy'])
        ->whereNumber('questionBank')
        ->name('question-banks.destroy');
    Route::patch('/question-banks/{questionBank}/toggle-status', [QuestionBankController::class, 'toggleStatus'])
        ->whereNumber('questionBank')
        ->name('question-banks.toggle-status');
    Route::post('/question-banks/bulk-upload', [QuestionBankController::class, 'bulkUpload'])->name('question-banks.bulk-upload');
    Route::get('/question-banks/download-template', [QuestionBankController::class, 'downloadTemplate'])->name('question-banks.download-template');
    Route::get('/api/question-banks/subjects/by-course', [QuestionBankController::class, 'getSubjectsByCourse'])->name('question-banks.subjects-by-course');
    Route::get('/api/question-banks/stats', [QuestionBankController::class, 'getQuestionStats'])->name('question-banks.stats');
    
    // Results management
    Route::get('/results', [ResultsController::class, 'index'])->name('results.index');
    Route::get('/results/{result}', [ResultsController::class, 'show'])->name('results.show');
    Route::get('/results/export/csv', [ResultsController::class, 'export'])->name('results.export');
    Route::get('/api/results/stats', [ResultsController::class, 'getStats'])->name('results.stats');

    // Reports (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/{report}/{format}', [ReportsController::class, 'export'])->name('reports.export');
    });
    
    // WhatsApp logs (monitoring)
    Route::get('/whatsapp-logs', [WhatsAppLogsController::class, 'index'])->name('whatsapp-logs.index');

    // Certificates management
    Route::get('/certificates', [CertificatesController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [CertificatesController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [CertificatesController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}', [CertificatesController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/{certificate}/preview', [CertificatesController::class, 'preview'])->name('certificates.preview');
    Route::post('/certificates/{certificate}/generate', [CertificatesController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/{certificate}/download', [CertificatesController::class, 'download'])->name('certificates.download');
    Route::patch('/certificates/{certificate}/revoke', [CertificatesController::class, 'revoke'])->name('certificates.revoke');
    Route::get('/api/batches/by-course', [CertificatesController::class, 'getBatchesByCourse'])->name('certificates.batches-by-course');
    Route::get('/api/certificates/stats', [CertificatesController::class, 'getStats'])->name('certificates.stats');
    
    // Settings (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/email-templates', [EmailTemplateController::class, 'index'])->name('settings.email-templates.index');
        Route::get('/settings/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('settings.email-templates.edit');
        Route::put('/settings/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('settings.email-templates.update');
        Route::post('/settings/email-templates/{emailTemplate}/reset', [EmailTemplateController::class, 'reset'])->name('settings.email-templates.reset');
        Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');
        Route::post('/settings/mail', [SettingsController::class, 'updateMail'])->name('settings.update-mail');
        Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::get('/clear-cache', [SettingsController::class, 'clearCacheGet'])->name('clear-cache');
        Route::post('/settings/optimize', [SettingsController::class, 'optimizeApplication'])->name('settings.optimize');
        Route::post('/settings/backup-database', [SettingsController::class, 'backupDatabase'])->name('settings.backup-database');
        Route::get('/settings/export-data', [SettingsController::class, 'exportData'])->name('settings.export-data');
        // Staff users (admin & reception management)
        Route::get('/settings/users', [UserManagementController::class, 'index'])->name('settings.users.index');
        Route::get('/settings/users/create', [UserManagementController::class, 'create'])->name('settings.users.create');
        Route::post('/settings/users', [UserManagementController::class, 'store'])->name('settings.users.store');
        Route::get('/settings/users/{user}/edit', [UserManagementController::class, 'edit'])->name('settings.users.edit');
        Route::put('/settings/users/{user}', [UserManagementController::class, 'update'])->name('settings.users.update');
        Route::get('/settings/users/{user}/change-password', [UserManagementController::class, 'showChangePassword'])->name('settings.users.change-password');
        Route::post('/settings/users/{user}/change-password', [UserManagementController::class, 'changePassword'])->name('settings.users.change-password.post');
    });
    
});

// Reception routes (same as admin but without payment approval)
Route::middleware(['auth', 'role:reception'])->prefix('admin')->name('admin.')->group(function () {
    // Same routes as admin but with different permissions
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::get('/enrollments', [StudentPortalController::class, 'enrollments'])->name('enrollments');
    Route::get('/payments', [StudentPortalController::class, 'payments'])->name('payments');
    Route::get('/assessments', [StudentPortalController::class, 'assessments'])->name('assessments');
    Route::get('/assessments/{assessment}/take', [StudentPortalController::class, 'takeAssessment'])->name('assessments.take');
    Route::get('/assessments/{assessment}/start', [StudentPortalController::class, 'startAssessment'])->name('assessments.start');
    Route::post('/assessments/{assessment}/submit', [StudentPortalController::class, 'submitAssessment'])->name('assessments.submit');
    Route::get('/assessments/results/{result}', [StudentPortalController::class, 'showAssessmentResult'])->name('assessments.show');
    Route::get('/certificates', [StudentPortalController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{certificate}/view', [StudentPortalController::class, 'viewCertificate'])->name('certificates.view');
    Route::get('/certificates/{certificate}/preview', [StudentPortalController::class, 'previewCertificate'])->name('certificates.preview');
    Route::get('/certificates/{certificate}/download', [StudentPortalController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/payments/{payment}/receipt/pdf', [StudentPortalController::class, 'downloadReceiptPdf'])->name('payments.receipt.pdf');
    Route::get('/payments/{payment}/receipt', [StudentPortalController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::get('/id-card', [StudentPortalController::class, 'idCard'])->name('id-card');
    Route::get('/id-card/download', [StudentPortalController::class, 'downloadIdCard'])->name('id-card.download');
});
