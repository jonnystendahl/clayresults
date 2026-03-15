#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

if ! command -v php >/dev/null 2>&1; then
    echo "php is not installed or not available in PATH." >&2
    exit 1
fi

if ! command -v npm >/dev/null 2>&1; then
    echo "npm is not installed or not available in PATH." >&2
    exit 1
fi

cleanup() {
    local exit_code=$?

    trap - EXIT INT TERM

    if [[ -n "${vite_pid:-}" ]] && kill -0 "$vite_pid" 2>/dev/null; then
        kill "$vite_pid" 2>/dev/null || true
        wait "$vite_pid" 2>/dev/null || true
    fi

    if [[ -n "${artisan_pid:-}" ]] && kill -0 "$artisan_pid" 2>/dev/null; then
        kill "$artisan_pid" 2>/dev/null || true
        wait "$artisan_pid" 2>/dev/null || true
    fi

    exit "$exit_code"
}

trap cleanup EXIT INT TERM

echo "Starting Laravel development server..."
php artisan serve &
artisan_pid=$!

echo "Starting Vite dev server..."
npm run dev &
vite_pid=$!

wait -n "$artisan_pid" "$vite_pid"