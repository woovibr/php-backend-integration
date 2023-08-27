<?php

namespace App\Services\OpenPix;

use OpenPix\PhpSdk\Client;

static $client;

if (is_null($client)) {
    $client = makeClient();
}

return $client;

/**
 * Make a client.
 */
function makeClient(): Client
{
    $env = [];

    if (file_exists($envPath = __DIR__ . "/../../env.php")) {
        $env = require_once $envPath;
    }

    $appID = $env["openpix-php-sdk"]["appID"] ?? "";
    if (empty($apiUrl)) $apiUrl = $_SERVER["HTTP_X_OPENPIX_APPID"] ?? "";
    if (empty($appID)) $appID = getenv("OPENPIX_APPID");
    if (empty($appID)) {
        die("You need to submit the App ID. You can submit using the env.php file, the `X-OpenPix-AppID` header or the `OPENPIX_APP_ID` environment variable.");
    }

    // @see https://developers.openpix.com.br/docs/sdk/php/sdk-php-customized-http-requests#customizando-a-uri-base
    $apiUrl = $env["openpix-php-sdk"]["apiUrl"] ?? "";
    if (empty($apiUrl)) $apiUrl = $_SERVER["HTTP_X_OPENPIX_API_URL"] ?? "";
    if (empty($apiUrl)) $apiUrl = getenv("OPENPIX_API_URL");
    if (empty($apiUrl)) $apiUrl = "https://api.woovi.com";

    return Client::create($appID, $apiUrl);
}