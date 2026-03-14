---
applyTo: "app/**/*.php,routes/**/*.php,database/**/*.php"
description: "Use when working on Laravel backend code, including routes, controllers, requests, models, migrations, auth flows, validation, and Eloquent relationships."
---

# Laravel Backend Instructions

- Use Laravel conventions and built-in features before custom abstractions.
- Prefer `php artisan make:`-style structure even if files are created manually.
- Keep business flow readable: route -> controller -> request validation -> model.
- Put validation in Form Requests when rules are more than trivial.
- Prefer named routes and route model binding.
- Prefer Eloquent relationships over manual joins for normal application code.
- Use authorization checks for any record scoped to a user.
- Keep controllers focused on orchestration, not heavy logic.
- For schema changes, create migrations with reversible `down()` methods.
- Match existing naming style in this repo: concise controllers, singular model names, plural table names.

For this project specifically:
- Runtime database is MySQL.
- User-owned data must never leak across accounts.
- Training results should remain simple CRUD unless broader analytics is requested.