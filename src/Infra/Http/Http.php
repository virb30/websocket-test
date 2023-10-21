<?php

declare(strict_types=1);

namespace App\Infra\Http;

interface Http
{
    public function listen(): void;
    public function on(string $method, string $uri, callable $callback);
}
