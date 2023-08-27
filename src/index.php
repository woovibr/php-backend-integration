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
 * Configure the pages of our system.
 */
$resultForWebServer = require_once __DIR__ . "/Core/Router.php";

return $resultForWebServer;