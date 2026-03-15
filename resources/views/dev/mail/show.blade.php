@extends('layouts.app', ['title' => 'View Mail | ClayResults'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Development tools</div>
                        <h1 class="h2 fw-bold mb-0">{{ $message['subject'] }}</h1>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('dev.mail.index') }}">Back to inbox</a>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="result-card p-4 h-100">
                            <div class="section-label mb-2">From</div>
                            <div class="fw-semibold">{{ $message['mail_from'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="result-card p-4 h-100">
                            <div class="section-label mb-2">To</div>
                            <div class="fw-semibold">{{ implode(', ', $message['rcpt_tos']) }}</div>
                        </div>
                    </div>
                </div>

                @if (! empty($message['html_body']))
                    <div class="result-card p-4 mb-4">
                        <div class="section-label mb-2">HTML body</div>
                        <div class="border rounded-4 p-3 bg-white">{!! $message['html_body'] !!}</div>
                    </div>
                @endif

                @if (! empty($message['text_body']))
                    <div class="result-card p-4 mb-4">
                        <div class="section-label mb-2">Text body</div>
                        <pre class="mb-0 text-wrap" style="white-space: pre-wrap;">{{ $message['text_body'] }}</pre>
                    </div>
                @endif

                <div class="result-card p-4">
                    <div class="section-label mb-2">Raw message</div>
                    <pre class="mb-0 text-wrap" style="white-space: pre-wrap;">{{ $message['raw'] }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection