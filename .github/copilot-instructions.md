# ClayResults AI Guidance

This repository is a Laravel 12 application for multi-user clay shooting training results.

Core stack:
- PHP 8.2+
- Laravel 12
- MySQL for app runtime
- Blade templates for UI
- Bootstrap 5 for styling and components
- Vite for frontend assets

Default development rules:
- Follow Laravel conventions before inventing custom structure.
- Keep controllers thin and use Form Requests for non-trivial validation.
- Prefer Eloquent relationships, route model binding, and named routes.
- Use Blade and Bootstrap 5. Do not introduce React, Vue, Livewire, or Alpine unless explicitly requested.
- Preserve the existing visual direction and avoid replacing Bootstrap with another UI approach.
- Protect all user-owned data with authorization or ownership checks.
- Prefer feature tests for user-facing behavior, especially auth and multi-user boundaries.
- Fix only the requested scope unless a nearby issue directly blocks the task.

Application-specific rules:
- Training results belong to individual authenticated users.
- Users must only be able to view and modify their own results.
- Use clear Swedish/English clay shooting discipline names already established in the app unless asked to change them.
- Keep forms server-rendered and simple to maintain.
- If database changes are needed, create migrations instead of editing schema manually.

When adding a feature, usually update:
- routes
- controller
- request validation
- model or relationship
- Blade view
- feature tests

When reviewing or editing code, prioritize:
- authorization correctness
- validation correctness
- Laravel convention alignment
- regression risk in routes and views
- test coverage for important flows