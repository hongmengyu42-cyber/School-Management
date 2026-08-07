<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = ['student_id', 'subject_id', 'date', 'status'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /** 'Present' and 'Late' both count as attended for warning-threshold math. */
    public function isPresentOrLate(): bool
    {
        return in_array($this->status, ['Present', 'Late'], true);
    }
}
