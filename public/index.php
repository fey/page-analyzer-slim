<?php

use Dotenv\Dotenv;

use function Feycot\PageAnalyzer\App\buildApp;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$app = buildApp();

$app->run();
