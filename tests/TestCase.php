<?php

namespace Feycot\PageAnalyzer\Tests;

use Carbon\Carbon;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseManager;
use Nekofar\Slim\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Selective\TestTrait\Traits\RouteTestTrait;

use function Feycot\PageAnalyzer\App\buildApp;
use function Feycot\PageAnalyzer\Schema\drop;
use function Feycot\PageAnalyzer\Schema\load;

class TestCase extends BaseTestCase
{
    use AppTestTrait;
    use RouteTestTrait;

    protected Manager $db;

    protected function setUp(): void
    {
        $this->setUpApp(buildApp());

        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '/../dependencies.php');
        $container = $builder->build();

        $this->db = $db = $container->get('db');
        drop();
        load();

        $this->urlId = $db->table('urls')->insertGetId([
            'name' => 'http://test.hexlet',
            'created_at' => Carbon::now()
        ]);

        $db->table('url_checks')->insert([
            'url_id' => $this->urlId,
            'status_code' => 200,
            'created_at' => Carbon::now()
        ]);
    }

    protected function urlFor(string $name, array $data = []): string
    {
        return $this->app->getRouteCollector()->getRouteParser()->urlFor($name, $data);
    }
}
