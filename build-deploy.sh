#!/usr/bin/env bash
#
# Builds a clean upload bundle for cPanel.
#
#   bash build-deploy.sh
#
# Produces build/dacca-delight-deploy.zip containing only what the server
# needs — no local tooling, no dev dependencies, no caches.
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUT="$ROOT/build"
STAGE="$OUT/stage"
PHP="${PHP_BIN:-php}"
COMPOSER="${COMPOSER_BIN:-composer}"

echo "==> Cleaning previous build"
rm -rf "$STAGE"
mkdir -p "$STAGE"

echo "==> Installing production dependencies (no dev)"
# Dev packages (PHPUnit, Faker, vfsStream) have no business on a live server.
"$PHP" "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --working-dir="$ROOT"

echo "==> Copying application"
for item in app public writable vendor composer.json composer.lock spark preload.php LICENSE; do
  cp -r "$ROOT/$item" "$STAGE/"
done

echo "==> Stripping caches and local state from writable/"
rm -rf "$STAGE/writable/debugbar" "$STAGE/writable/logs" "$STAGE/writable/session" "$STAGE/writable/cache"
mkdir -p "$STAGE/writable"/{cache,logs,session,uploads,debugbar}
# CI4 ships an index.html in each writable dir to stop directory listing.
for d in cache logs session uploads debugbar; do
  printf '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>\n' \
    > "$STAGE/writable/$d/index.html"
done

echo "==> Keeping uploaded product photos"
if [ -d "$ROOT/public/uploads/products" ]; then
  mkdir -p "$STAGE/public/uploads/products"
fi

echo "==> Production .env"
if [ -f "$ROOT/.env.production" ]; then
  cp "$ROOT/.env.production" "$STAGE/.env"
  echo "    .env written from .env.production — REMEMBER to fill in the DB block"
else
  echo "    WARNING: .env.production missing; upload a .env by hand"
fi

echo "==> Removing anything that should never ship"
find "$STAGE" -name '.DS_Store' -delete 2>/dev/null || true
rm -rf "$STAGE/tests" "$STAGE/.github" "$STAGE/phpunit.dist.xml" "$STAGE/builds"

echo "==> Zipping"
mkdir -p "$OUT"
ZIP="$OUT/dacca-delight-deploy.zip"
rm -f "$ZIP"
if command -v zip >/dev/null 2>&1; then
  ( cd "$STAGE" && zip -qr "$ZIP" . -x '*.git*' )
else
  # No zip binary (common on Windows/Git Bash) — PHP's ZipArchive does the job.
  "$PHP" -r '
    $stage = $argv[1]; $zipPath = $argv[2];
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        fwrite(STDERR, "Could not create zip\n"); exit(1);
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        $rel = ltrim(str_replace("\\", "/", substr($file->getPathname(), strlen($stage))), "/");
        if ($rel === "" || strpos($rel, ".git") === 0) { continue; }
        $file->isDir() ? $zip->addEmptyDir($rel) : $zip->addFile($file->getPathname(), $rel);
    }
    $zip->close();
    echo "    ", $zip->numFiles, " entries\n";
  ' "$STAGE" "$ZIP"
fi

echo
echo "Bundle: $OUT/dacca-delight-deploy.zip"
du -sh "$OUT/dacca-delight-deploy.zip"
echo
echo "Restoring dev dependencies locally..."
"$PHP" "$COMPOSER" install --no-interaction --working-dir="$ROOT" >/dev/null
echo "Done."
