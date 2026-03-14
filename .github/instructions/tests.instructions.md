---
applyTo: "tests/**/*.php"
description: "Use when creating or updating PHPUnit or Laravel feature tests for authentication, CRUD flows, authorization, and database-backed behavior."
---

# Testing Instructions

- Prefer feature tests for web flows and auth-protected behavior.
- Test the behavior that matters to users, not framework internals.
- Cover authorization boundaries whenever data belongs to a specific user.
- Assert redirects, session messages, and database state when relevant.
- Keep test names descriptive and behavior-focused.
- Use factories where available.
- Do not over-mock Laravel internals for standard application flows.

For this project specifically:
- Prioritize tests for login-required routes.
- Prioritize tests that confirm one user cannot edit or delete another user's result.
- When adding new training-result behavior, update feature tests in the same change.