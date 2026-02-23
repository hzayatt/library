@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="container">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Catalog
        </a>
    </div>

    <div class="book-detail">
        {{-- Cover --}}
        <div class="book-detail-cover">
            <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}">

            <div class="book-actions-panel">
                @if($book->is_available)
                    <span class="badge badge-available" style="font-size:0.85rem;padding:0.4rem 0.8rem;">
                        ✓ {{ $book->available_copies }} of {{ $book->total_copies }} available
                    </span>
                @else
                    <span class="badge badge-unavailable" style="font-size:0.85rem;padding:0.4rem 0.8rem;">
                        All copies checked out
                    </span>
                @endif

                @auth
                    {{-- Reading List --}}
                    <form method="POST" action="{{ route('reading-list.toggle', $book) }}">
                        @csrf
                        <button type="submit" class="btn {{ $inReadingList ? 'btn-secondary' : 'btn-outline' }} btn-block">
                            <svg viewBox="0 0 24 24" fill="{{ $inReadingList ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" width="16"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            {{ $inReadingList ? 'Remove from List' : 'Add to Reading List' }}
                        </button>
                    </form>

                    @if($userActiveBorrowing)
                        <div class="alert alert-info" style="font-size:0.8rem;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            You have this book until {{ $userActiveBorrowing->due_at->format('M d, Y') }}
                        </div>
                    @endif
                @endauth

                @can('checkout books')
                @if($book->is_available && !$userActiveBorrowing)
                <button type="button" class="btn btn-success btn-block" onclick="document.getElementById('checkoutForm').style.display='block'; this.style.display='none';">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Check Out
                </button>
                <form id="checkoutForm" method="POST" action="{{ route('borrowings.checkout', $book) }}" style="display:none;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Member</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select member...</option>
                            @foreach(\App\Models\User::role('member')->where('is_active', true)->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any notes..."></textarea>
                    </div>
                    <p class="form-hint">Due in {{ $book->borrow_days }} days. Fine: ${{ $book->fine_per_day }}/day if overdue.</p>
                    <button type="submit" class="btn btn-success btn-block">Confirm Checkout</button>
                </form>
                @endif
                @endcan

                @can('edit books')
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-outline btn-sm" style="flex:1;">Edit</a>
                    @can('delete books')
                    <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Delete this book permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    @endcan
                </div>
                @endcan

                @can('use ai features')
                <div style="border-top:1px solid var(--gray-200); padding-top:0.75rem;">
                    <p style="font-size:0.75rem; color:var(--gray-500); margin-bottom:0.5rem;">AI Features</p>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <button class="btn btn-outline btn-sm" onclick="generateAiSummary({{ $book->id }})">
                            <span class="ai-badge" style="padding:0.15rem 0.4rem;font-size:0.65rem;">AI</span> Summary
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="generateAiTags({{ $book->id }})">
                            <span class="ai-badge" style="padding:0.15rem 0.4rem;font-size:0.65rem;">AI</span> Tags
                        </button>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        {{-- Details --}}
        <div>
            <div style="margin-bottom:0.5rem;">
                @if($book->genre) <span class="badge badge-genre">{{ $book->genre }}</span> @endif
            </div>
            <h1 style="font-size:2rem; font-weight:800; line-height:1.2; margin-bottom:0.5rem;">{{ $book->title }}</h1>
            <p style="font-size:1.1rem; color:var(--gray-600); margin-bottom:1rem;">by <strong>{{ $book->author }}</strong></p>

            @if($book->reviews->count())
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.5rem;">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($book->average_rating) ? '★' : '☆' }}
                    @endfor
                </div>
                <span style="font-weight:700;">{{ $book->average_rating }}</span>
                <span class="text-muted">({{ $book->reviews->count() }} reviews)</span>
            </div>
            @endif

            <dl style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:0.75rem 1.5rem;margin-bottom:1.5rem;">
                @if($book->isbn)
                <div><dt style="font-size:0.75rem;color:var(--gray-500);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">ISBN</dt><dd>{{ $book->isbn }}</dd></div>
                @endif
                @if($book->publisher)
                <div><dt style="font-size:0.75rem;color:var(--gray-500);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Publisher</dt><dd>{{ $book->publisher }}</dd></div>
                @endif
                @if($book->publication_year)
                <div><dt style="font-size:0.75rem;color:var(--gray-500);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Year</dt><dd>{{ $book->publication_year }}</dd></div>
                @endif
                @if($book->pages)
                <div><dt style="font-size:0.75rem;color:var(--gray-500);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pages</dt><dd>{{ $book->pages }}</dd></div>
                @endif
                @if($book->language)
                <div><dt style="font-size:0.75rem;color:var(--gray-500);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Language</dt><dd>{{ $book->language }}</dd></div>
                @endif
            </dl>

            @if($book->description)
            <div class="card mb-3">
                <div class="card-body">
                    <h3 style="font-size:0.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--gray-500);margin-bottom:0.75rem;">Description</h3>
                    <p style="line-height:1.8;color:var(--gray-700);">{{ $book->description }}</p>
                </div>
            </div>
            @endif

            {{-- AI Summary --}}
            @if($book->ai_summary)
            <div class="card mb-3" style="border-color:#c4b5fd;">
                <div class="card-header" style="background:#f5f3ff;">
                    <span class="ai-badge">✦ AI Summary</span>
                </div>
                <div class="card-body">
                    <p id="aiSummaryText" style="line-height:1.8;color:var(--gray-700);">{{ $book->ai_summary }}</p>
                </div>
            </div>
            @else
            <div id="aiSummaryContainer" style="display:none;" class="card mb-3" style="border-color:#c4b5fd;">
                <div class="card-header" style="background:#f5f3ff;">
                    <span class="ai-badge">✦ AI Summary</span>
                </div>
                <div class="card-body">
                    <p id="aiSummaryText" style="line-height:1.8;color:var(--gray-700);"></p>
                </div>
            </div>
            @endif

            {{-- AI Tags --}}
            @if($book->ai_tags && count($book->ai_tags))
            <div class="mb-3">
                <div style="font-size:0.8rem;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.5rem;">AI Tags</div>
                <div class="ai-tags" id="aiTagsContainer">
                    @foreach($book->ai_tags as $tag)
                        <span class="ai-tag">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @else
            <div id="aiTagsWrapper" style="display:none;" class="mb-3">
                <div style="font-size:0.8rem;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.5rem;">AI Tags</div>
                <div class="ai-tags" id="aiTagsContainer"></div>
            </div>
            @endif

            {{-- Active borrowers --}}
            @can('view borrowings')
            @if($activeBorrowings->count())
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title" style="font-size:1rem;">Currently Borrowed By</h3>
                </div>
                <div class="card-body" style="padding:0;">
                    <table>
                        <thead><tr><th>Member</th><th>Borrowed</th><th>Due</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($activeBorrowings as $b)
                            <tr>
                                <td>{{ $b->user->name }}</td>
                                <td>{{ $b->borrowed_at->format('M d') }}</td>
                                <td class="{{ $b->due_at->isPast() ? 'text-danger' : '' }}">{{ $b->due_at->format('M d, Y') }}</td>
                                <td>
                                    @can('checkin books')
                                    <form method="POST" action="{{ route('borrowings.checkin', $b) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">Check In</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @endcan

            {{-- Reviews --}}
            <div class="mb-3">
                <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1rem;">Reviews</h2>

                @auth
                @if(!$userReview)
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title" style="font-size:1rem;">Write a Review</h3></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reviews.store', $book) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <div class="stars-input" style="flex-direction:row-reverse;justify-content:flex-end;">
                                    @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                    <label for="star{{ $i }}">★</label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Review (optional)</label>
                                <textarea name="review" class="form-control" rows="3" placeholder="Share your thoughts..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-info" style="margin-bottom:1rem;">You've already reviewed this book ({{ $userReview->rating }}/5 stars).</div>
                @endif
                @endauth

                @if($book->reviews->count())
                    @foreach($book->reviews as $review)
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <img src="{{ $review->user->avatar_url }}" class="reviewer-avatar" alt="">
                                <div>
                                    <div class="reviewer-name">{{ $review->user->name }}</div>
                                    <div class="review-rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <span class="text-muted text-small">{{ $review->created_at->diffForHumans() }}</span>
                                @if(auth()->id() === $review->user_id || auth()->user()?->hasRole('admin'))
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm btn-icon" title="Delete review" onclick="return confirm('Delete this review?')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @if($review->review)
                        <p class="review-text">{{ $review->review }}</p>
                        @endif
                    </div>
                    @endforeach
                @else
                <div class="empty-state" style="padding:2rem;">
                    <p>No reviews yet. Be the first to review!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function generateAiSummary(bookId) {
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Generating...';

    try {
        const resp = await fetch(`/ai/books/${bookId}/summary`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await resp.json();
        if (data.success) {
            document.getElementById('aiSummaryText').textContent = data.summary;
            document.getElementById('aiSummaryContainer') && (document.getElementById('aiSummaryContainer').style.display = 'block');
            btn.textContent = '✓ Summary Generated';
        } else {
            btn.textContent = 'Error — Try Again';
            btn.disabled = false;
        }
    } catch {
        btn.textContent = 'Error — Try Again';
        btn.disabled = false;
    }
}

async function generateAiTags(bookId) {
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Generating...';

    try {
        const resp = await fetch(`/ai/books/${bookId}/tags`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await resp.json();
        if (data.success) {
            const container = document.getElementById('aiTagsContainer');
            container.innerHTML = data.tags.map(t => `<span class="ai-tag">${t}</span>`).join('');
            document.getElementById('aiTagsWrapper') && (document.getElementById('aiTagsWrapper').style.display = 'block');
            btn.textContent = '✓ Tags Generated';
        } else {
            btn.textContent = 'Error — Try Again';
            btn.disabled = false;
        }
    } catch {
        btn.textContent = 'Error — Try Again';
        btn.disabled = false;
    }
}
</script>
@endpush
@endsection
