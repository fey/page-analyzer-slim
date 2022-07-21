<?php

use Psr\Container\ContainerInterface;
use Slim\App;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

return [
    App::class => function (ContainerInterface $c) {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $app = AppFactory::createFromContainer($c);
        $app->addErrorMiddleware(true, true, true);

        return $app;
    },
    'config' => function () {
        return [
            'db' => [
                'url' => $_ENV['DATABASE_URL'] ?? ''
            ]
        ];
    },
    'db' => function (ContainerInterface $c) {
        $capsule = new Illuminate\Database\Capsule\Manager();
        $capsule->addConnection($c->get('config')['db']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    },
];
