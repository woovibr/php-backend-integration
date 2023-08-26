<?php

/**
 * In this example, we use a simple database using SQLite.
 */

static $db;

if (is_null($db)) {
    // @see https://www.php.net/manual/en/ref.pdo-sqlite.connection.php#105350
    $db = new PDO(
        "sqlite::memory:",
        null,
        null,
        [PDO::ATTR_PERSISTENT => true]
    );

    // Add necessary changes to database.
    $db->query("
        CREATE TABLE IF NOT EXISTS (
            id INTEGER PRIMARY KEY,
            value INTEGER,
            comment TEXT,
            status TEXT DEFAULT 'OPEN',
            brCode TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
}

return $db;