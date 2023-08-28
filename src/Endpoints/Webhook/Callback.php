<?php

namespace App\Endpoints\Webhook\Callback;

use PDO;
use OpenPix\PhpSdk\Client;

/**
 * Called when testing webhooks.
 */
const TEST_WEBHOOK_EVENT = "teste_webhook";

/**
 * Charge completed is when a charge is fully paid.
 */
const OPENPIX_CHARGE_COMPLETED_EVENT = "OPENPIX:CHARGE_COMPLETED";

/**
 * Charge completed is when a charge is fully paid.
 */
const OPENPIX_TRANSACTION_RECEIVED_EVENT = "OPENPIX:TRANSACTION_RECEIVED";

// Endpoint: POST `/webhook`.
return function (): void
{
    // Get a "raw" string containing the request body,
    // such as the JSON sent by the platform.
    $rawPayload = strval(file_get_contents("php://input"));
    
    $rawHeaders = getallheaders();
    // We handle headers as case-insensitive.
    $headers = array_change_key_case($rawHeaders, CASE_LOWER);

    $client = require_once __DIR__ . "/../../Services/OpenPix.php";

    allowRequestOnlyFromOpenPix($client, $rawPayload, $headers);

    $payload = json_decode($rawPayload, true);

    // Fires the appropriate handler for each webhook type.
    handleWebhook($payload);
};

/**
 * Allows requests only from the OpenPix platform.
 */
function allowRequestOnlyFromOpenPix(Client $client, string $rawPayload, array $headers): void
{
    $signature = getallheaders()["x-webhook-signature"] ?? "";

    if (! $client->webhooks()->isWebhookValid($rawPayload, $signature)) {
        http_response_code(400);
        echo json_encode([
            "errors" => [
                [
                    "message" => "Invalid webhook signature.",
                ],
            ],
        ]);
        exit;
    }
}

/**
 * Checks if the webhook is from when a charge was paid.
 */
function isChargePaidPayload(array $payload): bool
{
    $allowedEvents = [
        OPENPIX_CHARGE_COMPLETED_EVENT,
        OPENPIX_TRANSACTION_RECEIVED_EVENT,
    ];

    $isChargePaidEvent = ! empty($payload["event"])
        && in_array($payload["event"], $allowedEvents);

    return $isChargePaidEvent
        && ! empty($payload["charge"]["correlationID"])
        && is_string($payload["charge"]["correlationID"]);
}

/**
 * Handle webhook when a charge was paid.
 */
function handleChargePaidWebhook(array $payload): void
{
    $correlationID = $payload["charge"]["correlationID"];

    updateDonationStatusToPaid($correlationID);

    http_response_code(200);
    echo json_encode(["message" => "Success."]);
}

/**
 * Checks if the webhook is of test type.
 * 
 * The OpenPix platform can send a test webhook to verify the webhook URL.
 */
function isTestPayload(array $payload): bool
{
    return ! empty($payload["evento"])
        && $payload["evento"] === TEST_WEBHOOK_EVENT;
}

/**
 * Handles the test webhook.
 */
function handleTestWebhook(array $payload): void
{
    http_response_code(200);
    echo json_encode(["message" => "Success."]);
}

/**
 * Dispatch a handler by the webhook type.
 */
function handleWebhook(array $payload): void
{
    if (isChargePaidPayload($payload)) {
        handleChargePaidWebhook($payload);
        return;
    }

    if (isTestPayload($payload)) {
        handleTestWebhook($payload);
        return;
    }

    http_response_code(400);
    echo json_encode([
        "errors" => [
            [
                "message" => "Invalid webhook type.",
            ],
        ]
    ]);
}

/**
 * Update donation status to paid.
 */
function updateDonationStatusToPaid(string $correlationID): bool
{
    $db = require_once __DIR__ . "/../../Core/Database.php";

    $query = $db->prepare("UPDATE Donation SET status = 'PAID' WHERE correlationID = ?");
    return $query->execute([$correlationID]);
}