<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevMailInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_mail_inbox_lists_and_shows_captured_messages(): void
    {
        $messageId = 'test-message';
        $directory = storage_path('app/dev-mail');
        $path = $directory.'/'.$messageId.'.json';

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, json_encode([
            'id' => $messageId,
            'captured_at' => now()->toIso8601String(),
            'mail_from' => 'hello@example.com',
            'rcpt_tos' => ['jonny.stendahl@skjulet.se'],
            'subject' => 'Reset Password Notification',
            'text_body' => 'Use the link below to reset your password.',
            'html_body' => '<p>Use the link below to reset your password.</p>',
            'raw' => 'Raw email content',
        ], JSON_THROW_ON_ERROR));

        try {
            $this->get(route('dev.mail.index'))
                ->assertOk()
                ->assertSee('Captured mail inbox')
                ->assertSee('Reset Password Notification');

            $this->get(route('dev.mail.show', $messageId))
                ->assertOk()
                ->assertSee('Reset Password Notification')
                ->assertSee('jonny.stendahl@skjulet.se')
                ->assertSee('Raw email content');
        } finally {
            @unlink($path);
        }
    }
}