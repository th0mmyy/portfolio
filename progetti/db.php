<?php
// ── db.php ──────────────────────────────────────────────
// Include this file wherever you need a DB connection.
// Place it OUTSIDE the web root if possible, or protect
// it via .htaccess (deny from all).
// ────────────────────────────────────────────────────────

define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio');
define('DB_USER', 'root');       // ← change
define('DB_PASS', '');           // ← change
define('DB_CHARSET', 'utf8mb4');

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}