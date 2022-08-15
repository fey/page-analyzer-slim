<?php

namespace Feycot\PageAnalyzer\App;

use Carbon\Carbon;
use DI\ContainerBuilder;
use DiDom\Document;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\App as SlimApp;
use Feycot\PageAnalyzer\UrlValidator;
use GuzzleHttp\Exception\GuzzleException;
use Slim\Http\ServerRequest;
use Slim\Psr7\Request;

function registerRoutes(SlimApp $app)
{
    $app->get('/', function ($request, $response) {
        $flash = $this->get('flash')->getMessages();

        return $this->get('renderer')->render($response, 'root.phtml', ['flash' => $flash]);
    })->setName('root');

    $app->get('/urls', function ($request, $response) {
        $urls = $this->get('db')->table('urls')->select()->get();
        $flash = $this->get('flash')->getMessages();

        return $this->get('renderer')->render($response, 'urls/index.phtml', ['urls' => $urls, 'flash' => $flash]);
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

        $flash = $this->get('flash')->getMessages();
        return $this->get('renderer')->render($response, 'urls/show.phtml', [
            'url' => $url,
            'checks' => $checks,
            'flash' => $flash
        ]);
    })->setName('urls.show');

    $app->post('/urls', function (ServerRequest|Request $request, ResponseInterface $response) {
        $requestBody = $request->getParsedBody();
        $urlName = $requestBody['url']['name'];

        $errors = UrlValidator::validate($urlName);

        if ($errors) {
            throw new Exception($errors);
        }

        $parsedUrl = parse_url($urlName);
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

        $this->get('flash')->addMessage('success', 'Url успешно добавлен');
        return $response->withRedirect($url);
    })->setName('urls.store');

    $app->post('/urls/{url_id}/checks', function ($request, $response, $params) {
        $id = $params['url_id'];
        $redirectRoute = $this->get('router')->urlFor('urls.show', ['id' => $id]);
        $url = $this->get('db')->table('urls')->find($id);

        try {
            $client = new \GuzzleHttp\Client();
            $body = $client->get($url->name)->getBody()->getContents();
        } catch (GuzzleException) {
            $this->get('flash')->addMessage('danger', 'Произошла ошибка при проверке');
            return $response->withRedirect($redirectRoute);
        }

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
        $this->get('flash')->addMessage('success', 'Url успешно проверен');
        return $response->withRedirect($redirectRoute);
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
