<?php

use Psr\Container\ContainerInterface;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

return [
    'app' => function (ContainerInterface $c) {
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
    'renderer' => function (ContainerInterface $c) {
        $router = $c->get('router');

        $renderer = new PhpRenderer(__DIR__ . '/templates', ['router' => $router]);
        $renderer->setLayout('layout.phtml');

        return $renderer;
    },

    'router' => function (ContainerInterface $c) {
        $app = $c->get('app');

        return $app->getRouteCollector()->getRouteParser();
    }
];
