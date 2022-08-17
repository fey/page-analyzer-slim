<?php

namespace Feycot\PageAnalyzer\App;

use DI\ContainerBuilder;
use Slim\App;

function registerRoutes(App $app)
{
    require __DIR__ . '/../config/routes.php';
}

function buildApp(): App
{
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/../config/dependencies.php');
    $container = $builder->build();

    /** @var App $app */
    $app = $container->get('app');

    /** @var \Illuminate\Database\Capsule\Manager */
    $db = $container->get('db');
    $db->setAsGlobal();
    $db->bootEloquent();

    registerRoutes($app);

    return $app;
}
