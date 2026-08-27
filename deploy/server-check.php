<?php
/**
 * Server preflight for cPanel.
 *
 * Upload to public_html/, open https://moyanbakey.com/server-check.php,
 * fix whatever it reports, then DELETE THE FILE. It reveals PHP version and
 * path details that are of no use to anyone but you.
 *
 * It deliberately prints no credentials.
 */
header('Content-Type: text/plain; charset=utf-8');

$fail = 0;
$warn = 0;

function line(string $state, string $label, string $detail = ''): void
{
    global $fail, $warn;
    if ($state === 'FAIL') { $fail++; }
    if ($state === 'WARN') { $warn++; }
    printf("[%-4s] %-34s %s\n", $state, $label, $detail);
}

echo "Dacca Delights — server preflight\n";
echo str_repeat('=', 62), "\n\n";

// ---- PHP ------------------------------------------------------------
echo "PHP\n";
$php = PHP_VERSION;
line(version_compare($php, '8.2.0', '>=') ? 'OK' : 'FAIL', 'PHP >= 8.2', 'found ' . $php);

foreach (['intl', 'mbstring', 'json', 'curl', 'zip', 'gd', 'mysqlnd'] as $ext) {
    $needed = in_array($ext, ['gd'], true) ? 'WARN' : 'FAIL';
    line(extension_loaded($ext) ? 'OK' : $needed, "extension: {$ext}",
        $ext === 'gd' ? '(product photo uploads need this)' : '');
}

// ---- Application layout ---------------------------------------------
echo "\nLayout\n";
$here = __DIR__;
$candidates = [
    $here . '/../app/Config/Paths.php',
    $here . '/../dacca-delight-app/app/Config/Paths.php',
];
$appPath = null;
foreach ($candidates as $c) {
    if (is_file($c)) { $appPath = realpath($c); break; }
}
line($appPath ? 'OK' : 'FAIL', 'app/Config/Paths.php found', $appPath ? dirname($appPath, 3) : 'not beside or above public_html');

$autoload = $appPath ? dirname($appPath, 3) . '/vendor/autoload.php' : null;
line($autoload && is_file($autoload) ? 'OK' : 'FAIL', 'vendor/autoload.php', $autoload && is_file($autoload) ? '' : 'run composer install --no-dev');

$envFile = $appPath ? dirname($appPath, 3) . '/.env' : null;
line($envFile && is_file($envFile) ? 'OK' : 'FAIL', '.env present', $envFile && is_file($envFile) ? '' : 'upload .env.production as .env');

// ---- Writable --------------------------------------------------------
echo "\nWritable\n";
$writable = $appPath ? dirname($appPath, 3) . '/writable' : null;
if ($writable && is_dir($writable)) {
    foreach (['', '/cache', '/logs', '/session', '/uploads'] as $sub) {
        $p = $writable . $sub;
        line(is_dir($p) && is_writable($p) ? 'OK' : 'FAIL', 'writable' . ($sub ?: '/'),
            is_dir($p) ? (is_writable($p) ? '' : 'chmod 755 (or 775)') : 'missing');
    }
} else {
    line('FAIL', 'writable/', 'not found');
}

$uploads = $here . '/uploads/products';
line(is_dir($uploads) ? (is_writable($uploads) ? 'OK' : 'FAIL') : 'WARN',
    'public/uploads/products', is_dir($uploads) ? '' : 'created on first upload');

// ---- Database --------------------------------------------------------
echo "\nDatabase\n";
if ($envFile && is_file($envFile)) {
    $env = [];
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        if ($l[0] === '#' || !str_contains($l, '=')) { continue; }
        [$k, $v] = explode('=', $l, 2);
        $env[trim($k)] = trim(trim($v), "'\"");
    }
    $host = $env['database.default.hostname'] ?? '';
    $name = $env['database.default.database'] ?? '';
    $user = $env['database.default.username'] ?? '';
    $pass = $env['database.default.password'] ?? '';

    if (str_starts_with($name, 'REPLACE_') || $name === '') {
        line('FAIL', 'database credentials filled in', 'still the placeholder values');
    } else {
        $mysqli = @new mysqli($host, $user, $pass, $name);
        if ($mysqli->connect_error) {
            line('FAIL', 'database connects', 'check cPanel > MySQL Databases');
        } else {
            line('OK', 'database connects', $name);
            $t = $mysqli->query("SHOW TABLES");
            line($t && $t->num_rows > 0 ? 'OK' : 'FAIL', 'tables present',
                $t ? $t->num_rows . ' tables' : 'run: php spark migrate');
            $mysqli->close();
        }
    }

    $base = $env['app.baseURL'] ?? '';
    line($base !== '' && str_contains($base, 'moyanbakey.com') ? 'OK' : 'WARN',
        'app.baseURL', $base ?: 'not set');
    line(($env['CI_ENVIRONMENT'] ?? '') === 'production' ? 'OK' : 'WARN',
        'CI_ENVIRONMENT', $env['CI_ENVIRONMENT'] ?? 'not set');
} else {
    line('FAIL', 'database check', '.env not readable');
}

// ---- Rewrite ---------------------------------------------------------
echo "\nApache\n";
$mod = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules(), true) : null;
line($mod === null ? 'WARN' : ($mod ? 'OK' : 'FAIL'), 'mod_rewrite',
    $mod === null ? 'cannot detect (usually fine on cPanel)' : '');
line(is_file($here . '/.htaccess') ? 'OK' : 'FAIL', 'public .htaccess uploaded',
    is_file($here . '/.htaccess') ? '' : 'hidden files are easy to miss over FTP');

echo "\n", str_repeat('=', 62), "\n";
printf("%d failed, %d warnings\n", $fail, $warn);
echo $fail === 0
    ? "Ready. DELETE THIS FILE NOW.\n"
    : "Fix the FAIL lines above, reload, then delete this file.\n";
