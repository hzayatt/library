<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'issued_by', 'returned_to',
        'borrowed_at', 'due_at', 'returned_at',
        'status', 'fine_amount', 'fine_paid', 'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'active' && $this->due_at->isPast();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return (int) now()->diffInDays($this->due_at);
    }

    public function calculateFine(): float
    {
        if (!$this->is_overdue) return 0;
        return $this->days_overdue * $this->book->fine_per_day;
    }

    public function updateOverdueStatus(): void
    {
        if ($this->status === 'active' && $this->due_at->isPast()) {
            $this->status = 'overdue';
            $this->fine_amount = $this->calculateFine();
            $this->save();
        }
    }
}
