<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'name', 'weight_percent'];

    protected function casts(): array
    {
        return ['weight_percent' => 'decimal:2'];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'category_id');
    }
}
