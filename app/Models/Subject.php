<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'access_code',
        'subject_name',
        'teacher_id',
        'semester_id',
        'department_id',
        'room_number',
        'days_of_week',
        'time_slot',
    ];

    // ---- Relationships -------------------------------------------------

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function gradeCategories(): HasMany
    {
        return $this->hasMany(GradeCategory::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function disputeThreads(): HasMany
    {
        return $this->hasMany(DisputeThread::class);
    }

    // ---- Domain helpers --------------------------------------------------

    /**
     * Replaces legacy isSubjectTermLocked(): a subject with no semester
     * assigned is treated as unlocked. Every grade/attendance write path
     * (Policies + Form Requests) must check this before allowing mutation.
     */
    public function isLocked(): bool
    {
        return (bool) ($this->semester?->is_locked ?? false);
    }

    /** Human-readable term name, used in "this term is archived" messaging. */
    public function termName(): ?string
    {
        return $this->semester?->name;
    }

    public static function findByAccessCode(string $code): ?self
    {
        return static::where('access_code', $code)->first();
    }
}
