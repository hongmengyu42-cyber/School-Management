<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use App\Models\User;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\AssignmentSubmission;
use App\Models\QuizAttempt;
use App\Models\DisputeThread;
use App\Models\ConductRecord;
use App\Models\ExtracurricularActivity;
use App\Models\Invoice;
use App\Models\ParentStudentLink;
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'year_level',
        'department_id',
    ];

    // ---- Relationships -------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'enrollments');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function disputeThreads(): HasMany
    {
        return $this->hasMany(DisputeThread::class);
    }

    public function conductRecords(): HasMany
    {
        return $this->hasMany(ConductRecord::class);
    }

    public function extracurricularActivities(): HasMany
    {
        return $this->hasMany(ExtracurricularActivity::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function parentLinks(): HasMany
    {
        return $this->hasMany(ParentStudentLink::class);
    }

    /** Parent user accounts linked to this student. */
    public function parents(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            ParentStudentLink::class,
            'student_id',
            'id',
            'id',
            'parent_user_id'
        );
    }

    /**
     * Every account that should receive grade/attendance alerts for this
     * student: the student's own login plus every linked parent. Used by
     * GradePosted and AttendanceAlert so both notifications reach the same
     * audience without duplicating the lookup at each call site.
     */
    public function notifiableUsers(): \Illuminate\Support\Collection
    {
        return collect([$this->user])
            ->merge($this->parents)
            ->filter()
            ->unique('id')
            ->values();
    }
}
