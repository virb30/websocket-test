<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Infra\Http\SwooleHttp;

$http = new SwooleHttp();

$http->on("POST", "/{id}", function ($params, $body) {
    return [
        'params' => $params,
        'body' => $body
    ];
});

$http->on("GET", "/", function ($params, $body) {
    return [
        'params' => $params,
        'body' => $body
    ];
});

$http->listen();
