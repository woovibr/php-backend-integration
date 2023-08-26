<?php

/**
 * Load Composer.
 *
 * It will allow us to load the Woovi PHP SDK.
 * 
 * In the most famous frameworks, like Laravel, Symfony and CakePHP,
 * Composer is configured automatically.
 * 
 * Depending on the case, you will have to configure it manually,
 * as in WordPress.
 */
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Configure the HTTP routes of our application using a simple system.
 * 
 * For example, we can say that if the user enters the route (URI)
 * `/donation` with the `POST` method, it should trigger the file
 * at `./donation/create.php`, which will create a new donation.
 * 
 * On a routing system, you will configure user pages.
 * 
 * Example routing systems:
 *   - Laravel: https://laravel.com/docs/10.x/routing
 */

// Normalize the Request URI.
// Removes leading and trailing whitespace and slashes from the URI.
$requestUri = trim($_SERVER["REQUEST_URI"], "/\n\r\t\v\x00");

// The HTTP request method like `GET` and `POST`.
$requestMethod = $_SERVER["REQUEST_METHOD"];

// The route arguments, like some ID.
$routeArgs = explode("/", $requestUri);

// Endpoint: POST `/donation`
$hasRouteMatch = $requestUri === "donation"
    && $requestMethod === "POST";

if ($hasRouteMatch) {
    require_once __DIR__ . "/route/donation/create.php";
    exit;
}

// Endpoint: GET `/donation/{donationId}`
$hasRouteMatch = ! empty($routeArgs[0])
    && $routeArgs[0] === "donation"
    && ! empty($routeArgs[1])
    && $requestMethod === "GET";

if ($hasRouteMatch) {
    require_once __DIR__ . "/route/donation/getOne.php";
    exit;
}

/**
 * Indicate a 404 error to PHP built-in server.
 */
return false;
