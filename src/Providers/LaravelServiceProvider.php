<?php
namespace Vluzrmos\Enotas\Providers;

use Illuminate\Support\ServiceProvider;
use Vluzrmos\Enotas\Client\Enotas;
use Vluzrmos\Enotas\Resources\Produto;

class LaravelServiceProvider extends ServiceProvider
{
    protected $resources = [
        Produto::class,
        Cliente::class,
        Venda::class
    ];

    protected $aliases = [
        Enotas::class => 'enotas',
        Produto::class => 'enotas.produtos',
        Cliente::class => 'enotas.clientes',
        Venda::class => 'enotas.vendas'
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
        foreach($this->aliases as $class => $alias) {
            $this->app->alias($class, $alias);
        }
    }
}
