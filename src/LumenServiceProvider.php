<?php namespace Naraki\ElasticSearch;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use Naraki\ElasticSearch\Facades\ElasticSearchIndex;
use Naraki\ElasticSearch\Manager as ElasticSearchManager;

class LumenServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('elasticsearch', function () {
            return new ElasticSearchManager(
                [
                    'connection' => [
                        'hosts' => [
                            env('ELASTIC_SEARCH_HOST', '127.0.0.1:9200'),
                        ],
                        'retries' => env('ELASTIC_SEARCH_RETRIES', 3),
                        'logging' => [
                            'enabled' => env('ELASTIC_SEARCH_LOG', false),
                            'path' => storage_path(env('ELASTIC_SEARCH_LOG_PATH', 'logs/elastic.log')),
                            'level' => env('ELASTIC_SEARCH_LOG_LEVEL', 200),
                        ],
                    ],
                ]
            );
        });
    }

    public function boot()
    {
        $this->app['router']->group([
            'namespace' => 'Naraki\ElasticSearch\Controllers',
        ], function ($r) {
            $r->get('/search/{input}', ['uses' => 'Search@get']);
            $r->post('/search[/{source}]', ['uses' => 'Search@post']);
        });
    }
}