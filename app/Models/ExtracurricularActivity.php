<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularActivity extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'activity_name', 'role', 'achievement', 'date_recorded'];

    protected function casts(): array
    {
        return ['date_recorded' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
