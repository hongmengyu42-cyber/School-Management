<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'category_id',
        'label',
        'grade_value',
    ];

    protected function casts(): array
    {
        return ['grade_value' => 'decimal:2'];
    }

    // ---- Relationships -------------------------------------------------

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GradeCategory::class, 'category_id');
    }

    // ---- Domain logic ----------------------------------------------------

    /**
     * Replaces the legacy generated `remarks` column (which was hardcoded to
     * a 75% cutoff and could never read an admin-configurable value, since
     * MySQL generated columns can't reference another table).
     *
     * This accessor reads Setting::passingThreshold() live, so every place
     * that used to display `grades.remarks` — Admin/Teacher Manage Grades,
     * the Early Warning System, and the Parent dashboard — now just reads
     * `$grade->status` and automatically respects the configured threshold.
     */
    protected function status(): Attribute
    {
        return Attribute::get(
            fn () => (float) $this->grade_value >= Setting::passingThreshold()
                ? 'Passed'
                : 'Failed'
        );
    }
}
