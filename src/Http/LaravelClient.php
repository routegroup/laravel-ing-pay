<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\Request;
use Routegroup\IngPay\Http\Response;
use Routegroup\IngPay\Lib\Config;

/**
 * Sends through Laravel's own HTTP client, which is the point of the core
 * taking a client rather than making one: an integration keeps Http::fake(),
 * its middleware and its logging, instead of this package opening a second
 * connection pool beside them.
 *
 * It adds no credentials of its own — the token arrives in the request's
 * headers — so it stays a transport and nothing else.
 */
class LaravelClient implements Client
{
    public function __construct(private readonly Config $config) {}

    public function send(Request $request): Response
    {
        $answer = $this->pending($request)->send(
            $request->method,
            $request->url,
            $request->body === [] ? [] : ['json' => $request->body],
        );

        $headers = [];

        foreach ($answer->headers() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        return new Response($answer->status(), $answer->body(), $headers, $answer->reason() ?: null);
    }

    private function pending(Request $request): PendingRequest
    {
        if (! in_array($request->method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
            throw new InvalidArgumentException("The gateway is never asked for {$request->method}.");
        }

        return Http::asJson()
            ->acceptJson()
            ->timeout($this->config->timeout)
            ->connectTimeout($this->config->connectTimeout)
            ->withHeaders($request->headers);
    }
}
