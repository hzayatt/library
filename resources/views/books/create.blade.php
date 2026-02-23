@extends('layouts.app')

@section('title', 'Add Book')

@section('content')
<div class="container" style="max-width:800px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Add New Book</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Title <span>*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Book title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Author <span>*</span></label>
                        <input type="text" name="author" class="form-control" value="{{ old('author') }}" required placeholder="Author name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}" placeholder="978-0-000-00000-0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Genre</label>
                        <input type="text" name="genre" class="form-control" value="{{ old('genre') }}" placeholder="Fiction, Non-Fiction, etc." list="genre-list">
                        <datalist id="genre-list">
                            <option value="Fiction"><option value="Non-Fiction"><option value="Fantasy">
                            <option value="Science Fiction"><option value="Mystery"><option value="Romance">
                            <option value="Biography"><option value="History"><option value="Philosophy">
                            <option value="Self-Help"><option value="Dystopia"><option value="Thriller">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publisher</label>
                        <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}" placeholder="Publisher name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publication Year</label>
                        <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year') }}" min="1" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Language</label>
                        <input type="text" name="language" class="form-control" value="{{ old('language', 'English') }}" placeholder="English">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-control" value="{{ old('pages') }}" min="1" placeholder="Number of pages">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Copies <span>*</span></label>
                        <input type="number" name="total_copies" class="form-control" value="{{ old('total_copies', 1) }}" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Borrow Period (days)</label>
                        <input type="number" name="borrow_days" class="form-control" value="{{ old('borrow_days', 14) }}" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fine per Day ($)</label>
                        <input type="number" name="fine_per_day" class="form-control" value="{{ old('fine_per_day', 0.50) }}" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <p class="form-hint">JPG, PNG, GIF up to 2MB</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Book description or summary...">{{ old('description') }}</textarea>
                </div>
                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary btn-lg">Add Book</button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
