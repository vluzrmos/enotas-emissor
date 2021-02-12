<?php
namespace Vluzrmos\Enotas\Providers;

use Illuminate\Support\ServiceProvider;
use Vluzrmos\Enotas\HttpClient\Enotas;
use Vluzrmos\Enotas\Resources\Cliente;
use Vluzrmos\Enotas\Resources\Produto;
use Vluzrmos\Enotas\Resources\Venda;
use Vluzrmos\Enotas\Resources\Webhook;

class LaravelServiceProvider extends ServiceProvider
{
    protected $resources = [
        Produto::class,
        Cliente::class,
        Venda::class,
        Webhook::class,
    ];

    protected $aliases = [
        Cliente::class => 'enotas.clientes',
        Enotas::class => 'enotas',
        Produto::class => 'enotas.produtos',
        Venda::class => 'enotas.vendas',
        Webhook::class => 'enotas.webhooks',
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/enotas.php' => config_path('enotas.php'),
        ]);
    }
    
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/enotas.php',
            'enotas'
        );

        $this->registerEnotas();
        $this->registerResources();
        $this->registerAliases();
    }

    public function registerEnotas()
    {
        $this->app->singleton(Enotas::class, function () {
            $apiKey = $this->app->config['enotas.apiKey'];

            $enotas = new Enotas($apiKey);
            $enotas->useAsGlobalInstance();

            return $enotas;
        });
    }
    
    public function registerResources()
    {
        foreach ($this->resources as $resourceClass) {
            $this->app->bind($resourceClass, function () use ($resourceClass) {
                $instance = new $resourceClass();
                $instance->setEnotas($this->app[Enotas::class]);

                return $instance;
            });
        }
    }

    public function registerAliases()
    {
        foreach ($this->aliases as $class => $alias) {
            $this->app->alias($class, $alias);
        }
    }
}
