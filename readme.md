## Enotas Emissor

PHP Client para manipulação da API Emissor do Enotas. https://enotas.com.br/emissor/


### Standalone package
```php
include __DIR__.'/vendor/autoload.php';

use Vluzrmos\Enotas\Client\Enotas;
use Vluzrmos\Enotas\Resources\Produto;

//apiKey gerada no menu "Perfil" > "Acessar configurações para nerds"
$apiKey = 'XXXX-XXXXX-XXXXXX';
$enotas = new Enotas($apiKey);

$enotas->useAsGlobalInstance(); //to use that instance globally in all resources

$produtos = new Produto();

print_r($produtos->last()->toArray());
```

### Produtos

```php

$produtosService = new \Vluzrmos\Enotas\Resources\Produto();

$produtosService->all(); // Lista a página 0 dos produtos

```

### Clientes

```php

$clientesService = new \Vluzrmos\Enotas\Resources\Cliente();

$clientesService->all(); // Lista a página 0 dos clientes

```

### Vendas

```php

$vendasService = new \Vluzrmos\Enotas\Resources\Venda();

$vendasService->all(); // Lista a página 0 dos vendas

```


## Laravel Package

Laravel will autodiscorver the service provider "Vluzrmos\Enotas\Providers\LaravelServiceProvider".
To exports configuration files:

```bash
php artisan vendor:publish --provider=Vluzrmos\\Enotas\\Providers\\LaravelServiceProvider
```

# Resources

Cada resource (Produto, Vendas, Cliente ...) pode ser usado como um serviço para listar/inserir/atualizar os dados via api.

```php
$produto = new Produto([
    'nome' => 'Computador All-In-One',
    'valorTotal' => 3800.00
]);

$produto->save();

//Alterando o nome
$produto->nome = 'Computador HP';
$produto->save();


//Paginação

$produtoService = new Produto(); // ou laravel: app('enotas.produtos');

//filtros opcionais
$pagina = 0; //zero-based
$itensPorPagina = 999;
$ordenacao = 'createdAt desc';
$filter = "(contains(nome, 'computador') or contains(tags/nome, 'computador'))";

// Todos os produtos para os filtros acima
$produtoService->all($pagina, $itensPorPagina, $ordenacao, $filter);

//últimos 999 produtos
$produtoService->all();

//pagina especifica
$produtoService->all(0);
$produtoService->all(1);
$produtoService->all(2);

// Recuperando um resource pelo ID:

$produto = $produtoService->find($id);

```
## Resources Disponíveis

```
Vluzrmos\Enotas\Resources\Produto;
Vluzrmos\Enotas\Resources\Webhook;
Vluzrmos\Enotas\Resources\Cliente;
Vluzrmos\Enotas\Resources\Venda;
```

#### Environment (Laravel)

```dotenv
ENOTAS_API_KEY=XXX-XXXXX 
#ou
ENOTAS_EMISSOR_API_KEY=XXX-XXXXX
```

