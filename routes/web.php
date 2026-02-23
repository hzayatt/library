<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReadingListController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// Google SSO
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Books - public listing and details
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Borrowings
    Route::get('/borrowings', [BorrowingController::class, 'index'])
        ->name('borrowings.index')
        ->middleware('can:view borrowings');
    Route::post('/books/{book}/checkout', [BorrowingController::class, 'checkout'])
        ->name('borrowings.checkout')
        ->middleware('can:checkout books');
    Route::post('/borrowings/{borrowing}/checkin', [BorrowingController::class, 'checkin'])
        ->name('borrowings.checkin')
        ->middleware('can:checkin books');
    Route::post('/borrowings/{borrowing}/renew', [BorrowingController::class, 'renewBorrowing'])
        ->name('borrowings.renew')
        ->middleware('can:manage borrowings');
    Route::post('/borrowings/{borrowing}/pay-fine', [BorrowingController::class, 'payFine'])
        ->name('borrowings.pay-fine')
        ->middleware('can:manage borrowings');
    Route::get('/my-borrowings', [BorrowingController::class, 'myBorrowings'])->name('borrowings.my');

    // Reviews
    Route::post('/books/{book}/reviews', [BookReviewController::class, 'store'])
        ->name('reviews.store');
    Route::delete('/reviews/{review}', [BookReviewController::class, 'destroy'])
        ->name('reviews.destroy');

    // Reading List
    Route::get('/reading-list', [ReadingListController::class, 'index'])->name('reading-list.index');
    Route::post('/books/{book}/reading-list', [ReadingListController::class, 'toggle'])->name('reading-list.toggle');

    // AI Features
    Route::post('/ai/books/{book}/summary', [AiController::class, 'generateSummary'])
        ->name('ai.summary')
        ->middleware('can:use ai features');
    Route::post('/ai/books/{book}/tags', [AiController::class, 'generateTags'])
        ->name('ai.tags')
        ->middleware('can:use ai features');
    Route::get('/ai/recommendations', [AiController::class, 'recommendations'])->name('ai.recommendations');
    Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');

    // User management (Admin only)
    Route::middleware('can:view users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('can:manage users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});
