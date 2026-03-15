<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\View\View;

class DevMailController extends Controller
{
    public function index(): View
    {
        return view('dev.mail.index', [
            'messages' => collect(glob(storage_path('app/dev-mail/*.json')) ?: [])
                ->map(fn (string $path): array => $this->readMessage($path))
                ->sortByDesc('captured_at')
                ->values(),
        ]);
    }

    public function show(string $message): View
    {
        $path = storage_path('app/dev-mail/'.basename($message).'.json');

        abort_unless(is_file($path), 404);

        return view('dev.mail.show', [
            'message' => $this->readMessage($path),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function readMessage(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new FileNotFoundException($path);
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        return $decoded;
    }
}