# ClayResults

ClayResults is a Laravel application for managing clay shooting clubs, their members, and club communication.

The app is built around club management first. Clubs can manage memberships, public club information, news, events, board contacts, and membership renewal flows. Individual training results are still supported, but they are now an extra function within the wider club-management platform.

## What It Does

- Manages clubs, club memberships, and member roles
- Supports users belonging to multiple clubs with a selectable main club
- Publishes club news, events, board information, and membership renewal details
- Lets administrators manage users and club-related administration flows
- Stores personal training results as an additional user feature
- Keeps each user's training results private to their own account

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Blade templates
- Bootstrap 5
- Vite

## Quick Start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database and update your `.env` file before running migrations.

Example `.env` database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clayresults
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```

Then run:

```bash
php artisan migrate
```

## Local Development

Run both the Laravel backend and Vite in one terminal:

```bash
./dev-start.sh
```

The script starts `php artisan serve` and `npm run dev`, and stops the other process automatically if either one exits.

In local development, the script also starts a lightweight SMTP mail catcher on `127.0.0.1:1025`.
Open `http://127.0.0.1:8000/dev/mail` to read captured verification and password-reset emails.

If you open the repository in VS Code, the workspace also starts `./dev-start.sh` automatically through `.vscode/tasks.json`.

If you prefer running them separately, use two terminals.

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

This gives you the Laravel app on the PHP dev server and Vite asset rebuilding during development.

## MySQL Setup

Create a database and a user with access to it.

Example MySQL commands:

```sql
CREATE DATABASE clayresults CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'clayresults'@'localhost' IDENTIFIED BY 'change_this_password';
GRANT ALL PRIVILEGES ON clayresults.* TO 'clayresults'@'localhost';
FLUSH PRIVILEGES;
```

After that, put the same database name, username, and password into `.env` and run:

```bash
php artisan migrate
```

## Running Tests

The test suite uses SQLite in memory by default, so your local PHP CLI must have the SQLite extensions enabled.

Check your loaded PHP modules with:

```bash
php -m | grep -Ei "sqlite|pdo"
```

You should see `pdo_sqlite` and `sqlite3` in the output.

If they are missing on Ubuntu or Debian, install them with:

```bash
sudo apt update
sudo apt install php8.3-sqlite3
```

If your machine uses a different PHP package name, `php-sqlite3` is the usual fallback.

Then run the test suite with:

```bash
php artisan test
```

## Versioning And Releases

This repository now follows Semantic Versioning together with Conventional Commits.

- `fix:` commits bump the patch version, for example `0.1.0 -> 0.1.1`
- `feat:` commits bump the minor version, for example `0.1.0 -> 0.2.0`
- breaking changes bump the major version, for example `0.1.0 -> 1.0.0`

The current starting version is `0.1.0`.

Preview the next calculated release without changing files:

```bash
npm run release:dry
```

Create the next version bump and changelog entry from conventional commits:

```bash
npm run release
```

The release tooling uses `.versionrc.json` to group commit types into changelog sections.

You can also run the release flow from GitHub Actions. The repository includes `.github/workflows/release.yml`, which can be started manually from the Actions tab and also runs automatically for pushes to `main`.

With protected `main`, the workflow does not push release commits directly to the branch. Instead it:

- installs dependencies
- prepares the next `CHANGELOG.md`, `package.json`, and `package-lock.json` changes with `standard-version`
- creates or updates a release pull request named `chore(release): x.y.z`
- creates the matching `vx.y.z` tag after that release pull request is merged to `main`

To allow the workflow to open release pull requests, enable GitHub Actions pull request creation in the repository settings:

- `Settings`
- `Actions`
- `General`
- `Workflow permissions`
- enable `Allow GitHub Actions to create and approve pull requests`

Because versioning starts at `0.1.0` now, create the first baseline tag after the `0.1.0` release commit is in git:

```bash
git tag v0.1.0
git push origin v0.1.0
```

After that, future `npm run release` commands will calculate the next version from commits after the latest release tag.

## Deployment

For deployment, this app should be served as a normal Laravel production site.

Typical production steps:

1. Install PHP, Composer, Node.js, MySQL, and a web server such as Nginx or Apache.
2. Clone the repository onto the server.
3. Run `composer install --no-dev --optimize-autoloader`.
4. Run `npm install` and `npm run build`.
5. Create the production `.env` file with the correct app URL, database credentials, and mail settings if needed.
6. Run `php artisan key:generate` if the app key is not already set.
7. Run `php artisan migrate --force`.
8. Run `php artisan storage:link` if you later add public file uploads.
9. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
10. Point the web server document root to the `public/` directory.

Recommended Laravel optimization commands for production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

On Apache or Nginx, make sure PHP can write to:

- `storage/`
- `bootstrap/cache/`

## Web Server Notes

- The web server should serve the `public/` directory, not the project root.
- If you use Apache, enable URL rewriting.
- If you use Nginx, route all non-file requests to `public/index.php`.
- Use HTTPS in production.

If you need to give an existing user administrator access, run:

```bash
php artisan users:make-admin user@example.com
```