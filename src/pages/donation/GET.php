<?php

// Endpoint: `GET /donation/{donationId}`
return function (int $donationId): void
{
    // Load the database.
    $db = require_once __DIR__ . "/../../db.php";

    $donation = findDonationById($db, $donationId);

    if (empty($donation)) {
        http_response_code(404);
        exit;
    }

    http_response_code(200);
    echo json_encode($donation);
};

/**
 * Return donation by ID or a empty array if not found.
 */
function findDonationById(PDO $db, int $donationId): array
{
    $query = $db->prepare("SELECT * FROM Donation WHERE id = ?");
    $query->execute([$donationId]);

    $result = $query->fetch();

    if ($result === false) return [];

    return $result;
}