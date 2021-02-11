<?php

namespace Vluzrmos\Enotas\Resources;

class Produto extends AbstractResource
{
    const TIPO_NFE_SERVICO = 0;
    const TIPO_NFE_PRODUTO = 1;

    protected $endpoint = 'produtos';
}
