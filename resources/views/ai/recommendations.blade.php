@extends('layouts.app')

@section('title', 'AI Features')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <span class="ai-badge" style="font-size:0.9rem;vertical-align:middle;margin-right:0.5rem;">✦ AI</span>
                AI-Powered Features
            </h1>
            <p class="page-subtitle">Personalized recommendations and intelligent assistance powered by Claude AI</p>
        </div>
        <a href="{{ route('ai.recommendations') }}" class="btn btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Refresh
        </a>
    </div>

    <div class="grid grid-2" style="margin-bottom:2rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="ai-badge" style="font-size:0.75rem;margin-right:0.35rem;">AI</span>
                    Personalized Recommendations
                </h2>
            </div>
            <div class="card-body">
                @if($recommendations && $recommendations->count())
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        @foreach($recommendations as $rec)
                        <div class="recommendation-card">
                            <div class="recommendation-cover">
                                <img src="{{ $rec['book']->cover_image_url }}" alt="{{ $rec['book']->title }}">
                            </div>
                            <div style="flex:1;">
                                <a href="{{ route('books.show', $rec['book']) }}" style="font-weight:700;font-size:0.95rem;color:var(--gray-900);">{{ $rec['book']->title }}</a>
                                <div class="text-muted text-small">{{ $rec['book']->author }}</div>
                                @if($rec['book']->genre)
                                    <span class="badge badge-genre" style="margin-top:0.25rem;">{{ $rec['book']->genre }}</span>
                                @endif
                                @if($rec['reason'])
                                <div class="recommendation-reason" style="margin-top:0.5rem;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $rec['reason'] }}
                                </div>
                                @endif
                                @if($rec['book']->is_available)
                                    <span class="badge badge-available" style="margin-top:0.5rem;display:inline-block;">Available</span>
                                @else
                                    <span class="badge badge-unavailable" style="margin-top:0.5rem;display:inline-block;">Checked Out</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @elseif($recommendations === null)
                    <div class="empty-state" style="padding:2rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h3>No recommendations yet</h3>
                        <p>Borrow some books first and we'll recommend books tailored to your taste!</p>
                        <div style="margin-top:1rem;"><a href="{{ route('books.index') }}" class="btn btn-primary">Browse Books</a></div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        AI recommendations require a valid Anthropic API key. Configure ANTHROPIC_API_KEY in your .env file.
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="ai-badge" style="font-size:0.75rem;margin-right:0.35rem;">AI</span>
                    Library Assistant Chat
                </h2>
            </div>
            <div class="card-body" style="padding:0;">
                <div id="pageChatMessages" style="height:300px;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:0.75rem;">
                    <div class="ai-message ai-message--bot" style="max-width:90%;">Hello! I'm your AI library assistant. I can help you find books, answer questions about the library, or suggest what to read next. What would you like to know?</div>
                </div>
                <div style="display:flex;padding:1rem;gap:0.5rem;border-top:1px solid var(--gray-100);">
                    <input type="text" id="pageChatInput" class="form-control" placeholder="Ask me anything about our books..." onkeypress="if(event.key==='Enter') pageSendMessage()">
                    <button onclick="pageSendMessage()" class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Features Overview --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Available AI Features</h2>
        </div>
        <div class="card-body">
            <div class="grid grid-3">
                <div style="text-align:center;padding:1.5rem;">
                    <div style="width:56px;height:56px;background:#ede9fe;border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#5b21b6" stroke-width="2" width="28"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:0.5rem;">Book Summaries</h3>
                    <p class="text-muted text-small">Generate concise AI-powered summaries for any book to help readers decide what to borrow.</p>
                </div>
                <div style="text-align:center;padding:1.5rem;">
                    <div style="width:56px;height:56px;background:#dbeafe;border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" width="28"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:0.5rem;">Smart Tagging</h3>
                    <p class="text-muted text-small">Automatically generate relevant tags and keywords for books to improve discoverability.</p>
                </div>
                <div style="text-align:center;padding:1.5rem;">
                    <div style="width:56px;height:56px;background:#d1fae5;border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#065f46" stroke-width="2" width="28"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:0.5rem;">Recommendations</h3>
                    <p class="text-muted text-small">Personalized book recommendations based on your reading history and preferences.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function pageSendMessage() {
    const input = document.getElementById('pageChatInput');
    const msg = input.value.trim();
    if (!msg) return;

    appendPageMessage(msg, 'user');
    input.value = '';

    const thinking = appendPageMessage('Thinking...', 'bot');

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
        thinking.textContent = 'Sorry, I\'m having trouble connecting. Please try again.';
    }
}

function appendPageMessage(text, type) {
    const container = document.getElementById('pageChatMessages');
    const div = document.createElement('div');
    div.className = `ai-message ai-message--${type}`;
    div.style.maxWidth = '90%';
    div.textContent = text;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
    return div;
}
</script>
@endpush
@endsection
