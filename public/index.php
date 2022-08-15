<?php

use Dotenv\Dotenv;

use function Feycot\PageAnalyzer\App\buildApp;
use function Feycot\PageAnalyzer\Schema\load;

require __DIR__ . '/../vendor/autoload.php';

session_start();
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$app = buildApp();
load();

$app->run();
