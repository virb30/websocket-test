<?php

declare(strict_types=1);

namespace App\Infra\WebSocket;

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

final class SwooleWebSocket
{
    private $server;

    public function __construct(private string $host = '0.0.0.0', private int $port = 8088)
    {
        $this->server = new Server($this->host, $this->port);
    }

    public function onOpen(callable $callback)
    {
        $this->server->on('open', function (Server $server, Request $req) use ($callback) {
            $connections = $server->connections;
            $origin = $req->fd;

            foreach ($connections as $connection) {
                if ($connection === $origin) {
                    continue;
                }

                $server->push($connection, json_encode($callback($req->fd)));
            }
        });
    }

    public function onMessage(callable $callback)
    {
        $this->server->on('message', function (Server $server, Frame $message) use ($callback) {
            $connections = $server->connections;
            $origin = $message->fd;

            foreach ($connections as $connection) {
                if ($connection === $origin) {
                    continue;
                }

                $server->push($connection, json_encode($callback($message->fd, $message->data)));
            }
        });
    }

    public function listen(): void
    {
        $this->server->start();
    }
}
