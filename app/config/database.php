<?php
/**
 * Database configuration – connects to PostgreSQL via PDO.
 */

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'postgres';
        $port = getenv('DB_PORT') ?: '5432';
        $db   = getenv('DB_NAME') ?: 'pitch_db';
        $user = getenv('DB_USER') ?: 'pitch_user';
        $pass = getenv('DB_PASS') ?: 'pitch_pass';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
