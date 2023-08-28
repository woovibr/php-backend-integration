<?php

namespace App\Endpoints\Donation\Create;

use PDO;
use function App\Endpoints\Donation\GetOne\findDonationById;

// For finding a donation.
require_once __DIR__ . "/GetOne.php";

// Endpoint: POST `/donation`.
return function (): void
{
    // Receive the donation sent to request body as JSON.
    $httpPayload = json_decode(file_get_contents("php://input"), true);

    // Respond all requests as JSON.
    header("Content-type: application/json");

    // Validate the request.
    validateRequest($httpPayload);

    // Load database.
    $db = require_once __DIR__ . "/../../Core/Database.php";

    // A unique ID for the Woovi charge.
    // @see https://developers.openpix.com.br/en/docs/concepts/correlation-id
    $correlationID = "php-backend-integration-" . bin2hex(random_bytes(8));

    $donation = $httpPayload + ["correlationID" => $correlationID];

    // Create the donation.
    $donationId = createDonationInDatabase($db, $donation);

    // Warn if a error ocurred.
    if (is_null($donationId)) {
        http_response_code(500);
        echo json_encode([
            "errors" => [
                [
                    "message" => "Cannot create donation in database.",
                ],
            ],
        ]);
        return;
    }

    // Load OpenPix PHP-SDK.
    $client = require_once __DIR__ . "/../../Services/OpenPix.php";

    // Create the charge on OpenPix/Woovi platform.

    $result = $client->charges()->create([
        "correlationID" => $correlationID,
        "value" => $httpPayload["value"],
        "comment" => $httpPayload["comment"],
    ]);

    if (! empty($result["error"])) {
        http_response_code(400);
        echo json_encode([
            "errors" => [
                [
                    "message" => $result["error"],
                ],
            ],
        ]);
    }

    $brCode = $result["brCode"];

    setDonationBrCode($db, $donationId, $brCode);

    $donation = findDonationById($db, $donationId);

    http_response_code(200);
    echo json_encode([
        "comment" => $donation["comment"],
        "value" => $donation["value"],
        "id" => $donationId,
        "status" => $donation["status"],
        "brCode" => $brCode,
        "correlationID" => $donation["correlationID"],
    ]);
};

/**
 * Create the Donation and return the ID or null if a error ocurrs.
 */
function createDonationInDatabase(PDO $db, array $data): ?int
{
    $query = $db->prepare(
        "INSERT INTO Donation (value, comment, correlationID) VALUES (?, ?, ?)"
    );

    $isSuccessful = $query->execute([
        $data["value"],
        $data["comment"],
        $data["correlationID"],
    ]);

    if (! $isSuccessful) return null;

    return $db->lastInsertId();
}

/**
 * Check if request is valid.
 */
function validateRequest($httpPayload): void
{
    $isRequestValid = ! empty($httpPayload)
        && is_array($httpPayload)
        && ! empty($httpPayload["comment"])
        && is_string($httpPayload["comment"])
        && ! empty($httpPayload["value"])
        && is_integer($httpPayload["value"]);

    if (! $isRequestValid) {
        http_response_code(500);
        echo json_encode([
            "errors" => [
                [
                    "message" => "Invalid request data.",
                ],
            ],
        ]);
        exit;
    }
}

/**
 * Set donation brCode.
 */
function setDonationBrCode(PDO $db, int $donationId, string $newBrCode): bool
{
    $query = $db->prepare("UPDATE Donation SET brCode = ? WHERE id = ?");
    return $query->execute([$newBrCode, $donationId]);
}