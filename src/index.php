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
 * Configure routes.
 *
 * By configuring the routes we are telling which pages a PHP code
 * will be fired.
 * 
 * We will configure, for example, the pages:
 * 
 * - `/donations` to trigger a file in `./donations` directory.
 * - `/webhooks` to trigger a file in `./webhooks` directory.
 * 
 * Go to this `router.php` file to see more.
 */
require_once __DIR__ . "/router.php";