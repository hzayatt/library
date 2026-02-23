# LibraryMS — Mini Library Management System

A full-featured Library Management System built with **Laravel 11**, **Blade**, and vanilla **CSS**, featuring Google SSO, role-based access control, and Claude AI integration.

---

## Features

### Core (Required)
- **Book Management** — Add, edit, delete books with full metadata (title, author, ISBN, publisher, genre, year, language, pages, cover image, copies, fine policy)
- **Check-out / Check-in** — Librarians can check books out to members and check them back in with automatic fine calculation
- **Search** — Full text search by title, author, ISBN, genre, description, publisher; filter by genre and availability; multi-column sorting
- **Authentication with SSO** — Email/password + Google OAuth via Laravel Socialite

### Roles & Permissions (Spatie Laravel Permission)
| Role | Capabilities |
|------|-------------|
| **Admin** | All permissions including user management, full CRUD |
| **Librarian** | Manage books, handle check-outs/check-ins, view reports |
| **Member** | Browse catalog, view own borrowings, write reviews, manage reading list |

### AI Features (Claude Haiku via Anthropic SDK)
- **AI Book Summaries** — One-click AI-generated summaries for any book (librarian/admin)
- **AI Smart Tagging** — Automatically generate relevant keywords/tags for books
- **Personalized Recommendations** — Claude analyzes borrowing history and suggests books
- **Library Chat Assistant** — Embedded floating chat widget powered by Claude, always available for help

### Extra Features
- **Dashboard** — Stats overview, recent borrowings, overdue alerts, popular books
- **Overdue Tracking** — Automatic status updates, fine calculation ($0.50/day default, configurable per book)
- **Fine Management** — Track unpaid fines, mark as paid
- **Book Reviews & Star Ratings** — Members can rate books 1-5 stars with optional text review
- **Reading List / Wishlist** — Bookmark books to read later
- **User Profiles** — Avatar (from Google or UI Avatars), borrowing stats, activity history
- **Loan Renewal** — Extend due dates for active borrowings
- **Soft Deletes** — Books are soft-deleted, preserving historical borrowing records
- **Responsive Design** — Works on desktop and mobile
- **12 Seeded Sample Books** — Ready to demo immediately

---

## Requirements

- PHP 8.2+
- Composer 2.x
- SQLite (default, zero-config) or MySQL/PostgreSQL

---

## Quick Start

```bash
# 1. Clone and install dependencies
git clone <repo-url> library
cd library
composer install

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env (see below)
# Minimum: nothing needed for basic operation (SQLite is default)
# Optional: add GOOGLE_CLIENT_ID/SECRET for SSO, ANTHROPIC_API_KEY for AI

# 4. Run migrations and seed demo data
php artisan migrate --seed

# 5. Create storage symlink (for book cover uploads)
php artisan storage:link

# 6. Start the dev server
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000)

---

## Environment Configuration

```env
# App
APP_NAME="LibraryMS"
APP_URL=http://localhost:8000

# Database (SQLite by default — no config needed)
DB_CONNECTION=sqlite

# Google OAuth (optional — for SSO login)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Anthropic Claude AI (optional — for AI features)
ANTHROPIC_API_KEY=your-anthropic-api-key
```

### Setting up Google OAuth (optional)
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a project → Enable "Google+ API" / "Google Identity"
3. Create OAuth 2.0 credentials → Web application
4. Add `http://localhost:8000/auth/google/callback` as an authorized redirect URI
5. Copy Client ID and Secret to `.env`

### Setting up Anthropic AI (optional)
1. Get an API key from [console.anthropic.com](https://console.anthropic.com/)
2. Add to `.env` as `ANTHROPIC_API_KEY=sk-ant-...`
3. AI features will activate automatically (summaries, tags, recommendations, chat)

---

## Demo Accounts

After running `php artisan migrate --seed`:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@library.com | admin123 |
| Librarian | librarian@library.com | librarian123 |
| Member | member@library.com | member123 |

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/
│   │   ├── LoginController.php      # Email login + logout
│   │   ├── RegisterController.php   # Registration
│   │   └── GoogleController.php     # Google OAuth SSO
│   ├── AiController.php             # Claude AI features
│   ├── BookController.php           # Book CRUD
│   ├── BookReviewController.php     # Star reviews
│   ├── BorrowingController.php      # Check-out/in, fines
│   ├── DashboardController.php      # Dashboard stats
│   ├── ReadingListController.php    # Wishlist
│   └── UserController.php           # User management
├── Models/
│   ├── Book.php                     # Book with search scopes
│   ├── Borrowing.php                # Loan with fine logic
│   ├── BookReview.php
│   ├── ReadingList.php
│   └── User.php                     # With Spatie HasRoles
└── Policies/
    └── BookPolicy.php               # Authorization

database/
├── migrations/                      # All schema migrations
└── seeders/
    ├── RolesAndPermissionsSeeder.php # 3 roles, 17 permissions
    └── BookSeeder.php               # 12 classic books

resources/views/
├── layouts/app.blade.php            # Main layout + AI chat widget
├── auth/                            # Login + register
├── books/                           # Index, show, create, edit
├── borrowings/                      # All loans, my loans
├── users/                           # Member management, profile
├── ai/                              # Recommendations page
└── reading-list/                    # Wishlist

public/css/app.css                   # ~500 lines custom CSS
routes/web.php                       # All application routes
```

---

## Key Technical Decisions

- **SQLite** for zero-config local development; swap to MySQL for production via `DB_CONNECTION=mysql`
- **Spatie Laravel Permission** for granular roles/permissions (17 permissions across 3 roles)
- **Laravel Socialite** handles the full Google OAuth flow including account linking
- **Soft deletes** on books preserve borrowing history integrity
- **Custom CSS** — no Tailwind/Bootstrap dependency; ~500 lines of clean, responsive CSS custom variables
- **Graceful AI degradation** — All AI features fail silently with user-friendly messages if API key is missing or API is unavailable
- **Recommendation caching** — AI recommendations are cached for 1 hour per user to minimize API calls

---

## License

MIT
