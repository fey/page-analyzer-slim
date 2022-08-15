<?php

use Psr\Container\ContainerInterface;
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
                'connection' => [
                    'development' => [
                        'driver' => 'sqlite',
                        'database' => __DIR__ . '/development.sqlite',
                    ],
                    'testing' => [
                        'driver' => 'sqlite',
                        'database' => __DIR__ . '/testing.sqlite',
                    ],
                    'pgsql' => [
                        'url' => $_ENV['DATABASE_URL'] ?? ''
                    ],
                ],
            ]
        ];
    },
    'db' => function (ContainerInterface $c) {
        $conn = $_ENV['DB_CONNECTION'];
        $capsule = new Illuminate\Database\Capsule\Manager();
        $connection = $c->get('config')['db']['connection'][$conn];
        $capsule->addConnection($connection);
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
    },

    'flash' => function () {
        return new \Slim\Flash\Messages();
    }
];
