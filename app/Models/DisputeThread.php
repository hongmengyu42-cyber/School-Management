<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One conversation ("thread") per student+subject context. Students see
 * only their own threads; teachers see only threads for subjects they
 * own — enforced via ThreadPolicy, not by this model alone.
 */
class DisputeThread extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'subject_id', 'status'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class, 'thread_id');
    }

    /**
     * Replaces messaging_helpers.php::findOrCreateThread(). This is what
     * makes "Message Teacher" a single click for students.
     */
    public static function findOrCreateFor(int $studentId, int $subjectId): self
    {
        return static::firstOrCreate(
            ['student_id' => $studentId, 'subject_id' => $subjectId, 'status' => 'Open'],
        );
    }
}
