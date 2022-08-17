<?php

use Dotenv\Dotenv;

use function Feycot\PageAnalyzer\App\buildApp;
use function Feycot\PageAnalyzer\Schema\load;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$app = buildApp();

$app->run();
