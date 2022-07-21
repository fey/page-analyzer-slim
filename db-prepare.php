<?php

use function Feycot\PageAnalyzer\Schema\drop;
use function Feycot\PageAnalyzer\Schema\load;
use DI\ContainerBuilder;


require 'vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/dependencies.php');
$container = $builder->build();
$container->get('db');
drop();
load();
