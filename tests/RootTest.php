<?php

namespace Feycot\PageAnalyzer\Tests;

use Nekofar\Slim\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;

use function Feycot\PageAnalyzer\App\buildApp;

class RootTest extends BaseTestCase
{
    use AppTestTrait;

    protected function setUp(): void
    {
        $this->setUpApp(buildApp());
    }

    public function testHomePage(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Анализатор страниц');
    }
}
