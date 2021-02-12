<?php

namespace Vluzrmos\Enotas\Resources;

class Venda extends AbstractResource
{
    protected $endpoint = 'vendas';

    protected $orderField = 'data';

    const EMITIR_NFE_AGORA = 0;
    const EMITIR_NFE_APOS_GARANTIA = 1;
    const EMITIR_NFE_NAO_EMITIR = 2;

    const MEIO_PAGAMENTO_NAO_INFORMADO = 0;
    const MEIO_PAGAMENTO_BOLETO = 1;
    const MEIO_PAGAMENTO_CARTAO_CREDITO = 2;
    const MEIO_PAGAMENTO_DEPOSITO = 3;
    const MEIO_PAGAMENTO_TRANSFERENCIA = 4;
    const MEIO_PAGAMENTO_BCASH = 5;
    const MEIO_PAGAMENTO_PAYPAL = 6;
    const MEIO_PAGAMENTO_DEBITO = 8;
    const MEIO_PAGAMENTO_OUTRO = 7;
}
