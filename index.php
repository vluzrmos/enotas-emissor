<?php

include __DIR__.'/vendor/autoload.php';

use Vluzrmos\Enotas\Client\Enotas;
use Vluzrmos\Enotas\Resources\Produto;
use Vluzrmos\Enotas\Resources\Webhook;
use Vluzrmos\Enotas\Resources\Cliente;
use Vluzrmos\Enotas\Resources\Venda;

$enotas = new Enotas(getenv('ENOTAS_API_KEY'));

$enotas->useAsGlobalInstance();

__print_resource('Produto', (new Produto)->first());
__print_resource('Cliente', (new Cliente)->first());
__print_resource('Venda', (new Venda)->first());
__print_resource('Webhook', (new Webhook)->first());


function __print_resource($title, $resource)
{
    echo '#### '.$title.' ####'.PHP_EOL;

    print_r($resource ? $resource->toArray() : []);

    echo PHP_EOL;
}
