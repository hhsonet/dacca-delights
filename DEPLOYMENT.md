# Deploying to cPanel

This is a standard CodeIgniter 4 app. Only the `public/` folder should ever be
web-accessible — everything else (`app/`, `system` via `vendor/`, `.env`, etc.)
must sit **outside** the document root so it can't be downloaded directly.

## Option A — you can point the domain's document root at `public/` (preferred)

Most cPanel accounts let you do this for addon domains and subdomains
(WHM/cPanel "Domains" screen has a "Document Root" field).

1. Upload the **entire project** (everything in this folder) to a directory
   *outside* `public_html`, e.g. `/home/USER/dacca-delight/`.
2. In cPanel → Domains, set the domain/subdomain's document root to
   `/home/USER/dacca-delight/public`.
3. Done — no code changes needed. `.htaccess` in `public/` already handles
   pretty URLs.

## Option B — document root is locked to `public_html` (typical for the primary domain on shared hosting)

1. Upload everything **except** the contents of `public/` to a folder above
   `public_html`, e.g. `/home/USER/dacca-delight-app/`.
2. Upload the *contents* of `public/` (not the folder itself) directly into
   `public_html/`.
3. Edit `public_html/index.php` (the copy you just uploaded) so its three
   `require` paths point at the app folder instead of `../`:

   ```php
   require FCPATH . '../dacca-delight-app/app/Config/Paths.php';
   // and inside paths.php-driven boot, systemDirectory / appDirectory
   ```

   Concretely, change the `FCPATH`-relative `..` segments in `index.php` from
   `dirname(__DIR__)` (one level up) to point at
   `/home/USER/dacca-delight-app` instead of the old project root. The file
   is short — open it and adjust the three path constants
   (`FCPATH`, and the `require` for `Paths.php`) accordingly.

Option A is strongly preferred: it needs zero code edits and keeps `app/`,
`writable/`, `.env`, and `vendor/` completely outside the web root.

## Every-deploy checklist

1. **PHP version**: this project requires PHP `^8.2`. In cPanel → "Select PHP
   Version" (MultiPHP Manager), set the domain to PHP 8.2 or newer, and make
   sure these extensions are enabled: `intl`, `mbstring`, `json`, `mysqlnd`
   (or your DB driver), `curl`, `zip`.
2. **`.env`**: don't upload your local `.env`. On the server, copy `env` to
   `.env` and set:
   ```
   CI_ENVIRONMENT = production
   app.baseURL = 'https://yourdomain.com/'
   database.default.hostname = localhost
   database.default.database = <cpanel_db_name>
   database.default.username = <cpanel_db_user>
   database.default.password = <cpanel_db_password>
   database.default.DBDriver = MySQLi
   ```
   cPanel MySQL databases/users are usually prefixed with your cPanel
   username (e.g. `cpuser_dacca`) — use the exact names from
   cPanel → MySQL Databases.
3. **`writable/` permissions**: make sure `writable/` (and its
   subfolders: `cache`, `logs`, `session`, `uploads`) are writable by the
   web server user — `chmod -R 755 writable` is usually enough on cPanel;
   use `775` if you hit permission errors.
4. **Install dependencies on the server** (or upload `vendor/` from your
   machine if the server has no SSH/Composer access):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
5. **Create the schema and seed it.** In cPanel → MySQL Databases create the
   database and user, put those credentials in `.env`, then run:
   ```bash
   php spark migrate
   php spark db:seed StorefrontSeeder
   ```
   The database must be `utf8mb4` — the catalogue contains `Bánh Mì` and `·`
   separators, which `latin1` will mangle. cPanel usually defaults to
   `utf8mb4` now, but confirm it. If you have no SSH access, run the two
   commands locally against the remote host, or export the local database and
   import the `.sql` dump through phpMyAdmin.
6. **Never commit/upload `.env`** — it holds credentials. Keep it out of git
   and out of any public backup.
7. Confirm `public/.htaccess` made it into the deploy (some FTP clients hide
   dotfiles by default) — it's required for clean URLs (no `index.php` in
   links).

## Storefront page notes

The storefront was imported from the Claude Design project and then split into
one route per function. `app/Views/storefront/layout.php` holds the shared
chrome (header, footer, cart drawer, chat) and `_logic.php` holds the shared
behaviour script; each page view supplies only its own section. Two things to
know before launch:

- **`app.baseURL` must be correct.** The page loads `support.js` and `logo.svg`
  through `base_url()`, so a wrong or empty `baseURL` breaks the whole page
  (blank sections with visible `{{ ... }}` placeholders). Set it to the real
  domain in the production `.env`.
- **It needs outbound CDN access at runtime.** `support.js` pulls React 18,
  ReactDOM 18 and Babel Standalone from `unpkg.com`, plus Google Fonts and
  `html2canvas` from jsDelivr. Product photos are hot-linked from
  `www.daccadelights.com`. If any of those are blocked or go away, the page
  degrades. Self-hosting the React/Babel bundle and the images is the fix if
  you'd rather not depend on third-party CDNs.

**Admin dashboard.** Lives at `/admin`, behind the `admin` filter
(`app/Filters/AdminFilter.php`). Staff accounts are in `admin_users` — a table
entirely separate from `customers`, with its own session key (`adminId`), so a
compromised shopper login can never reach the dashboard. Manages products,
categories, orders, customers, delivery zones, testimonials and gallery.
Deleting is restricted to `role = admin`; `staff` can edit but not destroy.

**Staff users** are managed at `/admin/users` — create, edit, change role,
reset passwords, deactivate and delete. That page is restricted to
`role = admin` by the `admin:admin` filter; staff are redirected away and the
nav link is hidden from them.

The filter re-reads the account from the database on every request rather than
trusting the role cached at login, so demoting, deactivating or deleting
someone takes effect against their **existing** session — they do not keep
their old privileges until they log out.

Guards that cannot be bypassed by editing the form (all enforced server-side):

- You cannot change your own role or deactivate/delete your own account.
- The last active administrator cannot be demoted, deactivated or deleted.
- Passwords must be at least 10 characters and are confirmed before saving.

Create the first staff login on the server with:

```bash
php spark make:admin "Full Name" you@example.com admin
```

It prompts for the password rather than taking it as an argument, so it stays
out of shell history. Run it again for the same email to reset that password.

**The storefront reads the database.** `app/Libraries/StorefrontData.php`
assembles the catalogue, zones, testimonials, gallery and the signed-in
customer's orders, and `BaseController` shares it with every shop view as
`$dd`. Editing a price or hiding a product in the dashboard changes the live
site immediately — there is no build step or cache to clear. Order history is
scoped to the signed-in customer; a guest gets an empty list.

Two consequences worth knowing:

- A product with `is_active = 0` disappears from the shop entirely.
- "Best sellers" are computed from real `order_items` history, not a fixed
  list, so they shift as orders come in. With no orders, the home page falls
  back to whatever is flagged `is_featured`.

**Accounts.** Signup/login write to `customers` (`/signup`, `/login`,
`POST /auth/signup`, `POST /auth/login`, `/auth/logout`). Passwords are hashed
with `password_hash()`/`PASSWORD_DEFAULT` — never stored or logged in plain
text. Seeded/guest customers have `password_hash = NULL` and cannot be logged
into. Login is throttled to 5 attempts per minute per IP and returns the same
message for a wrong password as for an unknown email, so the endpoint cannot be
used to discover which addresses are registered.

Two things to check before launch:

- **CSRF is not enabled globally.** `app/Config/Filters.php` still has the
  `csrf` filter commented out; the auth POST routes opt in individually in
  `Routes.php`. If you add any other state-changing POST route, either enable
  the filter globally or add `['filter' => 'csrf']` to that route.
- **Set `CI_ENVIRONMENT = production`** so cookies and error output behave.
  Sessions are file-based in `writable/session` — keep that directory writable
  and outside the web root.

Email verification is stubbed: `email_verified` exists but nothing sends a
code yet, so accounts are active immediately.

**Client-side state.** Because each function is now a real page load, three
things are bridged through browser storage rather than component state: the cart
(`localStorage.dd_cart2`), the checkout draft and chat log
(`sessionStorage.dd_draft`), and the invoice handed from checkout to
`/order/success` (`sessionStorage.dd_invoice`). If you later move orders
server-side, the invoice bridge is the first thing to replace.

The in-page "order assistant" chat calls `window.claude.complete`, which only
exists inside the Claude Design preview. In the browser it fails gracefully to
"I could not reach the kitchen just then" — wire it to your own backend (or
remove the widget) before launch. Checkout is likewise front-end only: payment
methods are selectable but the file itself notes "Gateway coming soon", and
orders/accounts/order history are in-memory demo data, not persisted. Cart
contents persist in `localStorage` under `dd_cart2`.
