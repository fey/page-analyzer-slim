<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../dependencies.php');
$container = $builder->build();

$app = $container->get(Slim\App::class);
$app->get('/', function ($request, $response) {
    dump($this->get('db')->table('users')->select()->get());
    return $response->write('Welcome to Slim!');
});

$app->run();
