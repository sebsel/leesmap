<?php

namespace App\Providers;

use GraphAware\Neo4j\Client\Client as Neo4j;
use GraphAware\Neo4j\Client\ClientBuilder;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\ServiceProvider;

class Neo4jServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Neo4j::class, function () {
            $uri = config('database.neo4j.default.protocol') . '://' .
                config('database.neo4j.default.username') . ':' .
                config('database.neo4j.default.password') . '@' .
                config('database.neo4j.default.host') . ':' .
                config('database.neo4j.default.port');

            return ClientBuilder::create()
                ->addConnection('default', $uri)
                ->build();
        });

        $this->app->alias(Neo4j::class, 'neo4j');
        $this->app->alias(Guzzle::class, 'http');
    }
}
