<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'author', 'published_year',
        'cover_path', 'cover_url', 'status', 'rating', 'started_at',
        'finished_at', 'review'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Resuelve la imagen de portada: prioriza el archivo subido, luego la URL.
     */
    public function getCoverImageAttribute(): ?string
    {
        if ($this->cover_path) {
            return asset('storage/' . $this->cover_path);
        }

        return $this->cover_url;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('author', 'like', "%{$term}%");
    }
}
