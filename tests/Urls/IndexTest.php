<?php

namespace Feycot\PageAnalyzer\Tests\Urls;

use DI\ContainerBuilder;
use Nekofar\Slim\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;

use function Feycot\PageAnalyzer\App\buildApp;
use function Feycot\PageAnalyzer\Schema\drop;
use function Feycot\PageAnalyzer\Schema\load;

class IndexTest extends BaseTestCase
{
    use AppTestTrait;

    protected function setUp(): void
    {
        $this->setUpApp(buildApp());

        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '/../../dependencies.php');
        $container = $builder->build();
        $container->get('db');
        drop();
        load();
    }

    public function testHomePage(): void
    {

        $this->get('/urls')
            ->assertOk()
            ->assertSee('Анализатор страниц');
    }
}
