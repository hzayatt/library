@extends('layouts.app')

@section('title', 'Edit: ' . $book->title)

@section('content')
<div class="container" style="max-width:800px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Edit Book</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Title <span>*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Author <span>*</span></label>
                        <input type="text" name="author" class="form-control" value="{{ old('author', $book->author) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Genre</label>
                        <input type="text" name="genre" class="form-control" value="{{ old('genre', $book->genre) }}" list="genre-list">
                        <datalist id="genre-list">
                            <option value="Fiction"><option value="Non-Fiction"><option value="Fantasy">
                            <option value="Science Fiction"><option value="Mystery"><option value="Romance">
                            <option value="Biography"><option value="History"><option value="Philosophy">
                            <option value="Self-Help"><option value="Dystopia"><option value="Thriller">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publisher</label>
                        <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publication Year</label>
                        <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year', $book->publication_year) }}" min="1" max="{{ date('Y') + 1 }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Language</label>
                        <input type="text" name="language" class="form-control" value="{{ old('language', $book->language) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-control" value="{{ old('pages', $book->pages) }}" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Copies <span>*</span></label>
                        <input type="number" name="total_copies" class="form-control" value="{{ old('total_copies', $book->total_copies) }}" min="1" required>
                        <p class="form-hint">Currently {{ $book->available_copies }} available</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Borrow Period (days)</label>
                        <input type="number" name="borrow_days" class="form-control" value="{{ old('borrow_days', $book->borrow_days) }}" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fine per Day ($)</label>
                        <input type="number" name="fine_per_day" class="form-control" value="{{ old('fine_per_day', $book->fine_per_day) }}" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cover Image</label>
                        @if($book->cover_image)
                            <div style="margin-bottom:0.5rem;">
                                <img src="{{ $book->cover_image_url }}" style="height:80px;border-radius:4px;" alt="">
                            </div>
                        @endif
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <p class="form-hint">Leave empty to keep current image</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $book->description) }}</textarea>
                </div>
                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
