<?php

namespace App\Test\TestCase\Action\Home;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Selective\TestTrait\Traits\HttpTestTrait;
use Selective\TestTrait\Traits\ContainerTestTrait;

use function Feycot\PageAnalyzer\App\buildApp;

class RootTest extends TestCase
{
    use ContainerTestTrait;
    use HttpTestTrait;

    public function setUp(): void
    {
        $this->app = buildApp();
    }

    public function testRoot(): void
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->app->handle($request);

        // Assert: Redirect
        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * Test invalid link.
     *
     * @return void
     */
    public function testPageNotFound(): void
    {
        $request = $this->createRequest('GET', '/nada');
        $response = $this->app->handle($request);

        // Assert: Not found
        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
