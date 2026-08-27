# Deploying to cPanel — moyanbakey.com

Everything below assumes the bundle produced by `build-deploy.sh`.

## 1. Build the bundle

From the project root:

```bash
bash build-deploy.sh
```

On Windows, point it at the binaries first:

```bash
PHP_BIN="/c/xampp/php/php.exe" COMPOSER_BIN="/c/laragon/bin/composer/composer.phar" bash build-deploy.sh
```

Produces `build/dacca-delight-deploy.zip` (~2.2 MB). It contains the app,
production-only dependencies and an empty `writable/` tree. It deliberately
excludes PHPUnit and friends, the debug toolbar cache, `tests/`, and the local
tooling folders (`.agents/`, `.claude/`, `.git/`).

## 2. Pick a layout

**Option A — you can point the domain at `public/` (preferred).**
In cPanel → *Domains*, set the document root for moyanbakey.com to
`/home/USER/moyanbakey/public`. Upload and extract the whole bundle to
`/home/USER/moyanbakey/`. No code changes.

**Option B — the document root is fixed to `public_html`.**

1. Extract the bundle to `/home/USER/dacca-delight-app/`
2. Move the *contents* of its `public/` folder into `public_html/`
   (including the hidden `.htaccess` — FTP clients often skip dotfiles)
3. Delete the now-empty `public/` from the app folder
4. Replace `public_html/index.php` with `deploy/index.public_html.php`
   from this repo — it points at `../dacca-delight-app/app/Config/Paths.php`

Nothing else needs editing: `app/Config/Paths.php` resolves the rest relative
to itself.

Option A is safer — under Option B the app folder still sits inside the home
directory, so make sure it is not itself web-served.

## 3. Database

cPanel → *MySQL Databases*:

1. Create a database and a user, then add the user to the database with
   **All Privileges**. Both get your account prefix, e.g. `moyanbak_dacca`.
2. Import the schema. Either run migrations over SSH:
   ```bash
   php spark migrate
   ```
   or, without SSH, export your local database and import it through
   phpMyAdmin:
   ```bash
   mysqldump -u root dacca_delight > dacca_delight.sql
   ```

## 4. Configure `.env`

The bundle ships `.env` already set for moyanbakey.com. Fill in the three
database placeholders and the Google secret:

```
database.default.database = moyanbak_xxxxx
database.default.username = moyanbak_xxxxx
database.default.password = ...
google.clientSecret       = GOCSPX-...
```

`.env` must sit in the application root (beside `app/`), **not** in
`public_html`.

## 5. Permissions

```bash
chmod -R 755 writable
chmod -R 755 public_html/uploads
```

Use `775` if the server still reports permission errors.

## 6. PHP version and extensions

cPanel → *Select PHP Version*: choose **8.2 or newer** and enable
`intl`, `mbstring`, `json`, `curl`, `zip`, `gd`, `mysqlnd`.

`gd` is required — product photo uploads re-encode images through it.

## 7. Google sign-in

In the Google Cloud console, add this to **Authorized redirect URIs** on the
OAuth client:

```
https://moyanbakey.com/auth/google/callback
```

If the consent screen is still in *Testing*, only listed test users can sign
in — publish it or add the accounts you need.

## 8. Verify

Upload `deploy/server-check.php` to `public_html/`, open
<https://moyanbakey.com/server-check.php>, fix anything marked FAIL, then
**delete the file**. It exposes PHP and path details that help nobody but you.

Then check by hand:

- <https://moyanbakey.com/> loads with products
- <https://moyanbakey.com/menu> lists the catalogue
- `/admin/login` signs in, `/admin/products` lists products
- Place a test order and confirm it appears under `/admin/orders`

## 9. After going live

- Set `CI_ENVIRONMENT = production` (the shipped `.env` already does).
  Never leave it on `development` — it prints stack traces, file paths and
  database details to visitors.
- Rotate the Google client secret if it has been shared in plain text.
- Change the admin password from anything used during development.
- Take a database backup before each subsequent deploy.

## Troubleshooting a 500

The generic error page is CI4 hiding the detail. Read
`writable/logs/log-YYYY-MM-DD.log` on the server — the `CRITICAL` line names
the cause. In rough order of likelihood:

1. `vendor/` missing or stale — re-upload it, or run
   `composer install --no-dev --optimize-autoloader`
2. `writable/` not writable
3. PHP below 8.2, or `intl`/`mbstring` missing
4. `.env` absent, or still holding `REPLACE_WITH_...` placeholders
5. Under Option B, `index.php` not repointed at the app folder
