<?php

// Import the Composer Autoloader to make the SDK classes accessible.
require 'vendor/autoload.php';

// Load our environment variables from the .env file:
(Dotenv\Dotenv::createImmutable(__DIR__))->load();

// Run system
$app = new App\Config\Application();
echo $app->initialize();
