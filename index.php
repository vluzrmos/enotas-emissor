<?php

include __DIR__.'/vendor/autoload.php';

use Vluzrmos\Enotas\Client\Enotas;
use Vluzrmos\Enotas\Resources\Produto;

$enotas = new Enotas('API_KEY');

$enotas->useAsGlobalInstance();

$produtos = new Produto();

print_r($produtos->last()->toArray());