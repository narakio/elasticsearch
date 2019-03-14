<?php namespace Naraki\ElasticSearch;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Naraki\ElasticSearch\Commands\Index;
use Naraki\ElasticSearch\Manager as ElasticSearchManager;

class ServiceProvider extends LaravelServiceProvider
{

    public function register()
    {
        $manager = new ElasticSearchManager(config('elastic-search'));
        $this->app->singleton('elasticsearch', function () use ($manager) {
            return $manager;
        });

        $this->app->singleton('elasticsearch.indexer', function () use ($manager) {
            return $manager->connection()->getIndexBuilder();
        });

        $this->app->singleton('command.elasticsearch.index', function () {
            return new Index();
        });

        $this->commands(['command.elasticsearch.index']);
    }

}