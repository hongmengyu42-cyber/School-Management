<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['author_id', 'title', 'body', 'audience'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** Scope: announcements visible to a given role (plus 'All'). */
    public function scopeVisibleToRole($query, string $role)
    {
        return $query->whereIn('audience', ['All', $role]);
    }
}
