# Agent Notes

This file collects short project notes that were previously kept in repository memory.

## Project Facts
- Laravel 12 application with custom Blade auth flow and Bootstrap 5 frontend theme.
- Frontend assets require `npm install` and Vite build.
- Production/app migration for `training_results` succeeded on 2026-03-14.

## Domain And Auth Notes
- Club memberships have an `is_club_admin` boolean.
- Members given a club-admin temporary password are marked `must_change_password` and are redirected to `/change-password` until they pick a personal password.

## Seed And Mail Notes
- Demo club/member seed data lives in `Database\Seeders\ClubTestDataSeeder` and is invoked from `DatabaseSeeder`.
- The demo seeder creates 5 fixed demo clubs with 1 to 5 seeded members each.
- Local development mail uses SMTP on `127.0.0.1:1025` with `scripts/dev_mail_catcher.py`.
- Captured local messages are browsable at `/dev/mail`.

## Migration Notes
- Migration `2026_03_15_020100_grant_admin_access_to_jonny_stendahl.php` promotes `jonny.stendahl@skjulet.se` to application administrator if that member exists.
- Migration `2026_03_15_020000_refactor_memberships_and_results_for_members.php` needs sqlite-safe index checks in tests because MySQL metadata queries must be bypassed outside MySQL.