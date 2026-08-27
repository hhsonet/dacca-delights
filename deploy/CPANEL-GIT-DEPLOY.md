# Deploying to moyanbakey.com by cloning from GitHub

This is the route to use if you are running `git clone` on the server rather
than uploading a zip.

## The one thing that must be right

**Only `public/` may be web-served.** The repository root holds `.env` (database
password, Google client secret) and `.git` (your entire source history). If the
document root points at the repository root, both are downloadable:

```
https://moyanbakey.com/.env          <- database + Google credentials
https://moyanbakey.com/.git/config   <- full repo, recoverable history
```

Two safe shapes:

**A. Point the document root at `public/` (preferred).**

```
/home/USER/moyanbakey/          <- git clone lands here
/home/USER/moyanbakey/public/   <- document root points HERE
```

cPanel → *Domains* → moyanbakey.com → set document root to
`/home/USER/moyanbakey/public`.

**B. Document root is fixed to `public_html`.**

Clone above the web root and serve `public/` from it:

```bash
cd ~
git clone https://github.com/hhsonet/dacca-delights.git dacca-delight-app
rm -rf public_html
ln -s ~/dacca-delight-app/public public_html
```

If the host does not allow a symlinked `public_html`, fall back to the split
layout in `CPANEL-DEPLOY.md` (Option B) instead.

There is a backstop either way: the repository root ships an `.htaccess` that
denies everything, and `public/.htaccess` grants access back for itself alone.
Verified — with the root web-served, `.env`, `.git/config`, `app/`, `writable/`
and `vendor/` all return **403**, while the site serves normally. Treat that as
a second line of defence, not a reason to skip the layout.

## Steps

```bash
cd ~
git clone https://github.com/hhsonet/dacca-delights.git moyanbakey
cd moyanbakey
```

`vendor/` is committed to this repository, so there is nothing to install. If
you would rather keep it out of git, run
`composer install --no-dev --optimize-autoloader` on the server instead.

### 1. Create `.env`

`.env` is gitignored and will **not** arrive with the clone — that is
deliberate. Create it from the production template:

```bash
cp .env.production .env
nano .env
```

Fill in the four placeholders: the three `database.default.*` values from
cPanel → *MySQL Databases* (names carry your account prefix, e.g.
`moyanbak_dacca`) and `google.clientSecret`.

### 2. Permissions

```bash
chmod -R 755 writable public/uploads
```

Use `775` if the server still reports permission errors.

### 3. Database

```bash
php spark migrate
```

No SSH? Export locally and import through phpMyAdmin:

```bash
mysqldump -u root dacca_delight > dacca_delight.sql
```

### 4. Create your admin login

```bash
php spark make:admin "Your Name" you@example.com admin
```

### 5. Google sign-in

Add to **Authorized redirect URIs** on the OAuth client:

```
https://moyanbakey.com/auth/google/callback
```

### 6. Verify

Copy `deploy/server-check.php` into `public/`, open
<https://moyanbakey.com/server-check.php>, fix anything marked FAIL, then
**delete it**.

Then confirm by hand that the secrets are not reachable:

```
https://moyanbakey.com/.env          -> must be 403 or 404
https://moyanbakey.com/../.env       -> must not resolve
```

## Updating later

```bash
cd ~/moyanbakey
git pull
php spark migrate
```

`.env` is untracked, so a pull never overwrites your credentials.

Customer-uploaded product photos are gitignored — they live only on the server
and survive a pull. Take a copy of `public/uploads/products/` before anything
destructive; git will not bring them back.

## Before real customers

- Rotate the Google client secret if it has ever been shared in plain text.
- Change any admin password used during development.
- Confirm `CI_ENVIRONMENT = production` in `.env`. On `development` the site
  prints stack traces, file paths and database details to visitors.
