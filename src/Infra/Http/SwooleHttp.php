<?php

declare(strict_types=1);

namespace App\Infra\Http;

use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

final class SwooleHttp implements Http
{
    private $app;
    private $router;
    private $dispatcher;

    public function __construct(private string $host = '0.0.0.0', private int $port = 8080)
    {
        $this->app = new Server($this->host, $this->port);
        $this->app->set(['hook_flags' => SWOOLE_HOOK_ALL]);

        $this->router = new RouteCollector(new RouteParser(), new DataGenerator());
    }


    public function listen(): void
    {
        $this->dispatcher = new Dispatcher($this->router->getData());
        $this->app->on("request", function (Request $request, Response $response) {
            $requestUri = $request->server['request_uri'];
            $requestMethod = $request->getMethod();

            $query = $request->get ?? [];
            $body = $request->rawContent();
            $body = empty($body) ? [] : json_decode($body, true);

            $result = $this->handleRequest($requestMethod, $requestUri, $query, $body);

            $response->header("Content-Type", "application/json");
            return $response->end(json_encode($result));
        });
        $this->app->start();
    }

    public function on(string $method, string $uri, callable $callback)
    {
        $this->router->addRoute(strtoupper($method), $uri, $callback);
    }

    private function handleRequest($method, $uri, array $query = [], array $body = [])
    {
        $dispatcherResults = $this->dispatcher->dispatch($method, $uri);

        $code = $dispatcherResults[0];
        $handler = $dispatcherResults[1];
        $vars = $dispatcherResults[2] ?? [];
        $query = array_merge($query, $vars);

        switch ($code) {
            case Dispatcher::NOT_FOUND:
                $result = [
                    'status' => 404,
                    'message' => 'Not Found',
                    'errors' => [
                        sprintf('The URI "%s" was not found', $uri)
                    ]
                ];
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $result = [
                    'status' => 405,
                    'message' => 'Method Not Allowed',
                    'errors' => [
                        sprintf('Method "%s" is not allowed', $method)
                    ]
                ];
                break;
            case Dispatcher::FOUND:
                $result = $handler($query, $body);
                break;
        }

        return $result;
    }
}
