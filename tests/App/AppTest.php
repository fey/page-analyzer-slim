<?php

namespace Feycot\PageAnalyzer\Tests;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use VCR\VCR;

use function Feycot\PageAnalyzer\App\buildApp;

class AppTest extends TestCase
{
    protected function setUp(): void
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'testing' => [
                'driver' => 'sqlite',
                'database' => __DIR__ . '/database/testing.sqlite',
            ],
        ]);

        $this->db = $capsule;
        $this->app = buildApp();
        Manager::connection()->beginTransaction();
    }

    public function testRootIndex(): void
    {
        $response = $this->app->handle(
            self::request('GET', '/')
        );

        self::assertOk($response);
    }

    public function testUrlsIndex(): void
    {
        $response = $this->app->handle(
            self::request('GET', '/urls')
        );

        self::assertOk($response);
    }

    public function testUrlsShow(): void
    {
        $response = $this->app->handle(
            self::request('GET', '/urls/1')
        );

        self::assertOk($response);
    }

    public function testUrlsCheckStore(): void
    {
        VCR::turnOn();
        \VCR\VCR::insertCassette('test_hexlet.yml');
        VCR::configure()->setMode('none');
        $checkData = [
            'url_id' => 1,
            'status_code' => '200',
            'title' => 'Awesome page',
            'description' => 'Statements of great people',
            'h1' => 'Do not expect a miracle, miracles yourself!',
            'created_at' => Carbon::now()
        ];

        $request = self::request('POST', '/urls/1/checks')
            ->withHeader('Accept', 'text/html')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        $response = $this->app->handle($request);

        self::assertRedirected($response);

        $urlCheck = $this->db->table('url_checks')->where($checkData)->first();
        self::assertNotNull($urlCheck);
    }

    public function testUrlStore(): void
    {
        $timestamp = time();
        $urlData = ['name' => "https://{$timestamp}.test"];

        $request = self::request('POST', '/urls')
            ->withHeader('Accept', 'text/html')
            ->withHeader('Content-Type', 'multipart/form-data')
            ->withParsedBody(['url' => $urlData]);

        $response = $this->app->handle($request);

        self::assertRedirected($response);

        $url = $this->db->table('urls')->where($urlData)->first();
        self::assertNotNull($url);
    }

    protected static function request(string $method, string $path): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }

    protected function tearDown(): void
    {
        VCR::turnOff();
        Manager::connection()->rollBack();
    }

    protected static function assertOk($response): void
    {
        self::assertEquals(200, $response->getStatusCode());
    }

    protected static function assertRedirected($response): void
    {
        self::assertEquals(302, $response->getStatusCode());
    }
}
