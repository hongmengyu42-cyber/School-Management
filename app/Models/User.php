<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

use App\Models\Department;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ParentStudentLink;
use App\Models\Announcement;
use App\Models\ActivityLog;
use App\Models\DisputeMessage;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if (empty($user->name) && !empty($user->full_name)) {
                $user->name = $user->full_name;
            }
        });
    }

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'department_id',
        'profile_picture',
        'theme_preference',
        'language',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---- Relationships -------------------------------------------------

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** Present only when role = Student. */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /** Present only when role = Teacher. */
    public function subjectsTaught(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    /** Present only when role = Parent. */
    public function parentLinks(): HasMany
    {
        return $this->hasMany(ParentStudentLink::class, 'parent_user_id');
    }

    public function linkedStudents(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Student::class,
            ParentStudentLink::class,
            'parent_user_id',
            'id',
            'id',
            'student_id'
        );
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function sentThreadMessages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class, 'sender_id');
    }

    // ---- Role helpers (replace legacy $_SESSION['role'] checks) --------

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'Teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    public function isParent(): bool
    {
        return $this->role === 'Parent';
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    /** Mirrors legacy redirectPathForRole() used after login / unauthorized access. */
    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'Admin' => 'admin.dashboard',
            'Teacher' => 'teacher.dashboard',
            'Student' => 'student.dashboard',
            'Parent' => 'parent.dashboard',
            default => 'login',
        };
    }
}
