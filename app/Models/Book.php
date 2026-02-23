<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'isbn', 'publisher', 'publication_year',
        'genre', 'description', 'language', 'pages', 'cover_image',
        'total_copies', 'available_copies', 'fine_per_day', 'borrow_days',
        'ai_summary', 'ai_tags', 'added_by',
    ];

    protected $casts = [
        'ai_tags' => 'array',
        'publication_year' => 'integer',
        'pages' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'fine_per_day' => 'decimal:2',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowing()
    {
        return $this->hasMany(Borrowing::class)->whereIn('status', ['active', 'overdue']);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function readingListEntries()
    {
        return $this->hasMany(ReadingList::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0;
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return 'https://placehold.co/200x300/4f46e5/white?text=' . urlencode($this->title);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('author', 'like', "%{$term}%")
              ->orWhere('isbn', 'like', "%{$term}%")
              ->orWhere('genre', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('publisher', 'like', "%{$term}%");
        });
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeByGenre($query, string $genre)
    {
        return $query->where('genre', $genre);
    }
}
