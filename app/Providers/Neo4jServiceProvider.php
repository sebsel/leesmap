<?php

namespace App\Providers;

use GraphAware\Neo4j\Client\Client as Neo4j;
use GraphAware\Neo4j\Client\ClientBuilder;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class Neo4jServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Neo4j::class, function () {
            return ClientBuilder::create()
                ->addConnection('default', 'http://neo4j:sebsebseb@localhost:7474') // Example for HTTP connection configuration (port is optional)
//                ->addConnection('bolt', 'https://neo4j:sebsebseb@localhost:7687') // Example for BOLT connection configuration (port is optional)
                ->build();
        });

        $this->app->alias(Neo4j::class, 'neo4j');

        $this->app->alias(Guzzle::class, 'http');
    }
}
