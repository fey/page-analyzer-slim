<?php

namespace Feycot\PageAnalyzer\App;

use Carbon\Carbon;
use DI\ContainerBuilder;
use DiDom\Document;
use Slim\App as SlimApp;
use Slim\Psr7\Request;

function registerRoutes(SlimApp $app)
{
    $app->get('/', function ($request, $response) {
        return $this->get('renderer')->render($response, 'root.phtml');
    })->setName('root');

    $app->get('/urls', function ($request, $response) {
        $urls = $this->get('db')->table('urls')->select()->get();
        return $this->get('renderer')->render($response, 'urls/index.phtml', ['urls' => $urls]);
    })->setName('urls.index');

    $app->get('/urls/{id}', function ($request, $response, $params) {
        $id = (int)$params['id'];

        $url = $this->get('db')->table('urls')->where('id', $id)->first();
        $checks = $this->get('db')->table('url_checks')
            ->distinct('url_id')
            ->orderBy('url_id')
            ->oldest()
            ->get()
            ->keyBy('url_id');

        return $this->get('renderer')->render($response, 'urls/show.phtml', ['url' => $url, 'checks' => $checks]);
    })->setName('urls.show');

    $app->post('/urls', function (Request $request, $response) {
        // TODO: validation
        $requestBody = $request->getParsedBody();
        $params = $requestBody['url'];
        $parsedUrl = parse_url($params['name']);
        $schema = $parsedUrl['scheme'];
        $normalizedUrl = mb_strtolower("{$schema}://{$parsedUrl['host']}");
        $url = $this->get('db')->table('urls')->where('name', $normalizedUrl)->first();

        if (!is_null($url)) {
            $id = $url->id;
        } else {
            $urlData = [
                'name' => $normalizedUrl,
                'created_at' => Carbon::now()
            ];
            $id = $this->get('db')->table('urls')->insertGetId($urlData);
        }

        $url = $this->get('router')->urlFor('urls.show', ['id' => $id]);
        return $response->withRedirect($url);
    })->setName('urls.store');

    $app->post('/urls/{url_id}/checks', function ($request, $response, $params) {
        $id = $params['url_id'];
        $url = $this->get('db')->table('urls')->find($id);

        $client = new \GuzzleHttp\Client();
        $body = $client->get($url->name)->getBody()->getContents();

        $document = new Document($body);
        $h1 = optional($document->first('h1'))->text();
        $title = optional($document->first('title'))->text();
        $description = optional($document->first('meta[name=description]'))->getAttribute('content');

        $data = [
            'url_id' => $url->id,
            'status_code' => $response->getStatusCode(),
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
            'created_at' => Carbon::now()
        ];

        $this->get('db')->table('url_checks')->insert($data);

        $url = $this->get('router')->urlFor('urls.show', ['id' => $id]);
        return $response->withRedirect($url);
    })->setName('urls.checks.store');
}

function buildApp(): SlimApp
{
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/../dependencies.php');
    $container = $builder->build();
    $app = $container->get('app');
    $container->get('db');

    registerRoutes($app);

    return $app;
}
