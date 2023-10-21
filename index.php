<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Infra\Http\SwooleHttp;
use App\Infra\WebSocket\SwooleWebSocket;

// $http = new SwooleHttp();

// $http->on("POST", "/{id}", function ($params, $body) {
//     return [
//         'params' => $params,
//         'body' => $body
//     ];
// });

// $http->on("GET", "/", function ($params, $body) {
//     return [
//         'params' => $params,
//         'body' => $body
//     ];
// });

// $http->listen();

$ws = new SwooleWebSocket('localhost');
$ws->onOpen(function ($id) {
    return [
        'type' => 'login',
        'text' => "Usuário $id entrou"
    ];
});
$ws->onMessage(function ($id, $data) {
    return [
        'type' => 'chat',
        'text' => $data
    ];
});
$ws->listen();
