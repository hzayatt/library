<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Library') — LibraryMS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>

{{-- Navigation --}}
<nav class="navbar">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            LibraryMS
        </a>

        <div class="navbar-menu">
            @auth
                <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">Books</a>
                @can('view borrowings')
                    <a href="{{ route('borrowings.index') }}" class="nav-link {{ request()->routeIs('borrowings.index') ? 'active' : '' }}">Borrowings</a>
                @endcan
                <a href="{{ route('borrowings.my') }}" class="nav-link {{ request()->routeIs('borrowings.my') ? 'active' : '' }}">My Books</a>
                <a href="{{ route('reading-list.index') }}" class="nav-link {{ request()->routeIs('reading-list.*') ? 'active' : '' }}">Reading List</a>
                <a href="{{ route('ai.recommendations') }}" class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">AI Features</a>
                @can('view users')
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
                @endcan
            @endauth
        </div>

        <div class="navbar-actions">
            @auth
                <div class="user-menu">
                    <button class="user-menu-toggle" onclick="toggleUserMenu()">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar">
                        <span>{{ auth()->user()->name }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <div class="user-menu-header">
                            <div class="user-menu-name">{{ auth()->user()->name }}</div>
                            <div class="user-menu-role">
                                @foreach(auth()->user()->roles as $role)
                                    <span class="badge badge-{{ $role->name }}">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="user-menu-item">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="user-menu-item user-menu-item--danger">Sign Out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success') || session('error') || $errors->any())
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endif

{{-- Main Content --}}
<main class="main-content">
    @yield('content')
</main>

{{-- AI Chat Widget --}}
@auth
<div class="ai-chat-widget" id="aiChatWidget">
    <button class="ai-chat-toggle" onclick="toggleChat()" title="Ask AI Assistant">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </button>
    <div class="ai-chat-panel" id="aiChatPanel" style="display:none;">
        <div class="ai-chat-header">
            <span>AI Library Assistant</span>
            <button onclick="toggleChat()">×</button>
        </div>
        <div class="ai-chat-messages" id="aiChatMessages">
            <div class="ai-message ai-message--bot">Hello! I'm your library assistant. Ask me anything about our books or get recommendations!</div>
        </div>
        <div class="ai-chat-input">
            <input type="text" id="aiChatInput" placeholder="Ask about books..." onkeypress="if(event.key==='Enter') sendMessage()">
            <button onclick="sendMessage()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
    </div>
</div>
@endauth

<footer class="footer">
    <div class="container">
        <p>&copy; {{ date('Y') }} LibraryMS — Built with Laravel &amp; Claude AI</p>
    </div>
</footer>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const menu = document.querySelector('.user-menu');
    if (menu && !menu.contains(e.target)) {
        document.getElementById('userMenuDropdown')?.classList.remove('open');
    }
});

function toggleChat() {
    const panel = document.getElementById('aiChatPanel');
    panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
}

async function sendMessage() {
    const input = document.getElementById('aiChatInput');
    const msg = input.value.trim();
    if (!msg) return;

    appendMessage(msg, 'user');
    input.value = '';

    const thinking = appendMessage('Thinking...', 'bot');

    try {
        const resp = await fetch('{{ route("ai.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message: msg }),
        });
        const data = await resp.json();
        thinking.textContent = data.response;
    } catch {
        thinking.textContent = 'Sorry, I\'m having trouble connecting.';
    }
}

function appendMessage(text, type) {
    const container = document.getElementById('aiChatMessages');
    const div = document.createElement('div');
    div.className = `ai-message ai-message--${type}`;
    div.textContent = text;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
    return div;
}
</script>

@stack('scripts')
</body>
</html>
