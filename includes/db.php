<?php

/**
 * Datenbankverbindung (PDO / MySQLi)
 * ====================================
 * Gibt ein PDO-Objekt zurück (Singleton-Muster).
 */
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $port = defined('DB_PORT') ? DB_PORT : 3306;
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (IS_DEV) {
                die('Datenbankfehler: ' . $e->getMessage());
            } else {
                die('Datenbankverbindung fehlgeschlagen. Bitte versuchen Sie es später erneut.');
            }
        }
    }
    return $pdo;
}
