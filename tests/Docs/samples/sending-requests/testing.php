<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\GetTransactionResponse;

$client = FakeClient::answering(GetTransactionResponse::factory()->make()->toArray());

app()->instance(Client::class, $client);

app(Api::class)->getTransaction('tx-1042');

expect($client->lastRequest()?->url)->toEndWith('/transaction/tx-1042');
// #endregion sample
