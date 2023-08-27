<?php

namespace App\Endpoints\Donation\GET;

use PDO;

// Endpoint: `GET /donation/{donationId}`
return function (int $donationId): void
{
    // Load the database.
    $db = require_once __DIR__ . "/../../db.php";

    // Respond all requests as JSON.
    header("Content-type: application/json");

    // Find the donation.
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

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result === false) return [];

    return $result;
}