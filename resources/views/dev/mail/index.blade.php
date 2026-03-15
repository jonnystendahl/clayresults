@extends('layouts.app', ['title' => 'Development Mail | ClayResults'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="content-panel p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Development tools</div>
                        <h1 class="h2 fw-bold mb-0">Captured mail inbox</h1>
                    </div>
                    <div class="text-secondary">{{ $messages->count() }} message{{ $messages->count() === 1 ? '' : 's' }}</div>
                </div>

                @if ($messages->isEmpty())
                    <div class="result-card p-4 p-lg-5 text-center">
                        <h2 class="h4 fw-semibold mb-2">No captured emails yet</h2>
                        <p class="text-secondary mb-0">Trigger a verification email or password reset to populate this inbox.</p>
                    </div>
                @else
                    <div class="vstack gap-3">
                        @foreach ($messages as $message)
                            <a class="result-card p-4 text-decoration-none text-reset" href="{{ route('dev.mail.show', $message['id']) }}">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-2">
                                    <div>
                                        <div class="fw-semibold">{{ $message['subject'] }}</div>
                                        <div class="text-secondary small">To: {{ implode(', ', $message['rcpt_tos']) }}</div>
                                    </div>
                                    <div class="text-secondary small">{{ \Illuminate\Support\Carbon::parse($message['captured_at'])->format('Y-m-d H:i:s') }}</div>
                                </div>
                                <div class="note-preview">{{ \Illuminate\Support\Str::limit(trim($message['text_body'] ?? strip_tags($message['html_body'] ?? '')), 180) }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection