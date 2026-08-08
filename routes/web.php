<?php
// use App\Models\User;

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BulkImportController;
use App\Http\Controllers\Admin\CascadingFilterController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ParentLinkController;
use App\Http\Controllers\Admin\ReportCardController as AdminReportCardController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\TeacherAssignmentController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentsController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\ConductController as TeacherConductController;
use App\Http\Controllers\Teacher\ExtracurricularController as TeacherExtracurricularController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\InboxController as TeacherInboxController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\QuizQuestionController as TeacherQuizQuestionController;
use App\Http\Controllers\Teacher\SubjectWorkspaceController;
use App\Http\Controllers\Teacher\SubmissionController as TeacherSubmissionController;
use App\Http\Controllers\Teacher\TimetableController as TeacherTimetableController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\MessageController as StudentMessageController;
use App\Http\Controllers\Student\QuizAttemptController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ReportCardController as StudentReportCardController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\ReportCardController as ParentReportCardController;
use App\Http\Controllers\Parent\StudentController as ParentStudentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TwoFactorSetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Step 3.2 scope: only the auth-adjacent routes.
| Fortify itself registers /login, /register, /forgot-password,
| /reset-password, /two-factor-challenge, etc. automatically — nothing to
| add here for those.
|
| Step 3.3 scope: Admin resource routes for foundational data. Teacher /
| Student / Parent routes land in later steps.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor-setup', [TwoFactorSetupController::class, 'show'])->name('two-factor.setup');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notificationId}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/', function () {
        return redirect()->route(request()->user()->dashboardRouteName());
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return redirect()->route(request()->user()->dashboardRouteName());
    });

    // Placeholder named routes so User::dashboardRouteName() has somewhere
    // to point during this step; each becomes a real controller in 3.3+.
    Route::get('/admin/dashboard', fn () => view('admin.dashboard'))
        ->middleware('role:Admin')->name('admin.dashboard');

    Route::middleware('role:Teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', fn () => view('teacher.dashboard'))->name('dashboard');
        Route::get('/timetable', [TeacherTimetableController::class, 'index'])->name('timetable');

        Route::get('/inbox', [TeacherInboxController::class, 'index'])->name('inbox.index');
        Route::get('/inbox/{thread}', [TeacherInboxController::class, 'show'])->name('inbox.show');
        Route::post('/inbox/{thread}/reply', [TeacherInboxController::class, 'reply'])->name('inbox.reply');

        Route::get('/subjects', [SubjectWorkspaceController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/{subject}', [SubjectWorkspaceController::class, 'show'])->name('subjects.show');

        Route::prefix('subjects/{subject}')->name('subjects.')->group(function () {
            Route::get('grades', [TeacherGradeController::class, 'index'])->name('grades.index');
            Route::post('grades', [TeacherGradeController::class, 'store'])->name('grades.store');
            Route::put('grades/{grade}', [TeacherGradeController::class, 'update'])->name('grades.update');
            Route::delete('grades/{grade}', [TeacherGradeController::class, 'destroy'])->name('grades.destroy');
            Route::post('grade-categories', [TeacherGradeController::class, 'storeCategory'])->name('grade-categories.store');
            Route::delete('grade-categories/{category}', [TeacherGradeController::class, 'destroyCategory'])->name('grade-categories.destroy');

            Route::get('attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
            Route::post('attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

            Route::resource('assignments', TeacherAssignmentsController::class)->except('show');

            Route::resource('quizzes', TeacherQuizController::class)->only(['index', 'create', 'store', 'destroy']);

            Route::get('conduct', [TeacherConductController::class, 'index'])->name('conduct.index');
            Route::post('conduct', [TeacherConductController::class, 'store'])->name('conduct.store');
            Route::delete('conduct/{record}', [TeacherConductController::class, 'destroy'])->name('conduct.destroy');

            Route::get('extracurricular', [TeacherExtracurricularController::class, 'index'])->name('extracurricular.index');
            Route::post('extracurricular', [TeacherExtracurricularController::class, 'store'])->name('extracurricular.store');
            Route::delete('extracurricular/{activity}', [TeacherExtracurricularController::class, 'destroy'])->name('extracurricular.destroy');
        });

        Route::get('assignments/{assignment}/submissions', [TeacherSubmissionController::class, 'index'])->name('assignments.submissions.index');
        Route::put('submissions/{submission}', [TeacherSubmissionController::class, 'update'])->name('submissions.update');

        Route::get('quizzes/{quiz}/questions', [TeacherQuizQuestionController::class, 'index'])->name('quizzes.questions.index');
        Route::post('quizzes/{quiz}/questions', [TeacherQuizQuestionController::class, 'store'])->name('quizzes.questions.store');
        Route::delete('quizzes/{quiz}/questions/{question}', [TeacherQuizQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    });

    Route::middleware('role:Student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');

        Route::get('/enroll', [StudentEnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enroll', [StudentEnrollmentController::class, 'store'])->name('enrollments.store');

        Route::get('/subjects', [StudentSubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/{subject}', [StudentSubjectController::class, 'show'])->name('subjects.show');
        Route::post('/subjects/{subject}/message-teacher', [StudentMessageController::class, 'startForSubject'])->name('subjects.message-teacher');

        Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades.index');
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');

        Route::get('/report-card', [StudentReportCardController::class, 'index'])->name('report-card.index');
        Route::get('/report-card/{semester}', [StudentReportCardController::class, 'show'])->name('report-card.show');

        Route::prefix('subjects/{subject}')->name('subjects.')->group(function () {
            Route::get('assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
            Route::get('assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
            Route::post('assignments/{assignment}', [StudentAssignmentController::class, 'store'])->name('assignments.store');

            Route::get('quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        });

        Route::get('quizzes/{quiz}/take', [QuizAttemptController::class, 'create'])->name('quizzes.take');
        Route::post('quizzes/{quiz}/take', [QuizAttemptController::class, 'store'])->name('quizzes.attempt');
        Route::get('quiz-attempts/{attempt}', [QuizAttemptController::class, 'show'])->name('quizzes.attempts.show');

        Route::get('/messages', [StudentMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{thread}', [StudentMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{thread}/reply', [StudentMessageController::class, 'reply'])->name('messages.reply');
    });

    Route::middleware('role:Parent')->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', ParentDashboardController::class)->name('dashboard');
        Route::get('/children/{student}', [ParentStudentController::class, 'show'])->name('children.show');

        Route::get('/children/{student}/report-card', [ParentReportCardController::class, 'index'])->name('children.report-card.index');
        Route::get('/children/{student}/report-card/{semester}', [ParentReportCardController::class, 'show'])->name('children.report-card.show');
    });

    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('academic-years', AcademicYearController::class)->except('show');

        Route::resource('semesters', SemesterController::class)->except('show');
        Route::post('semesters/{semester}/toggle-lock', [SemesterController::class, 'toggleLock'])
            ->name('semesters.toggle-lock');

        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');

        Route::get('students/{student}/report-card', [AdminReportCardController::class, 'index'])->name('students.report-card.index');
        Route::get('students/{student}/report-card/{semester}', [AdminReportCardController::class, 'show'])->name('students.report-card.show');

        Route::post('approve-users/{user}', UserApprovalController::class)->name('users.approve');
        Route::post('bulk-import-users', BulkImportController::class)->name('users.bulk-import');
        Route::post('assign-teacher', TeacherAssignmentController::class)->name('subjects.assign-teacher');
        Route::get('parent-links', [ParentLinkController::class, 'index'])->name('parent-links.index');
        Route::post('parent-links', [ParentLinkController::class, 'store'])->name('parent-links.store');
        Route::delete('parent-links/{parentLink}', [ParentLinkController::class, 'destroy'])->name('parent-links.destroy');

        Route::get('activity-logs', ActivityLogController::class)->name('activity-logs.index');

        Route::get('system-settings', [SystemSettingController::class, 'edit'])->name('system-settings.edit');
        Route::put('system-settings', [SystemSettingController::class, 'update'])->name('system-settings.update');

        Route::get('ajax/semesters-for-year', [CascadingFilterController::class, 'semestersForYear'])
            ->name('ajax.semesters-for-year');
        Route::get('ajax/subjects-for-semester', [CascadingFilterController::class, 'subjectsForSemester'])
            ->name('ajax.subjects-for-semester');
    });
});
