<?php

// Endpoint: `GET /donation/{donationId}`
return function (int $donationId): void
{
    // Load the database.
    $db = require_once __DIR__ . "/../../db.php";

    $donations = $db->query("SELECT * FROM Donation");

    var_dump($donations);
};