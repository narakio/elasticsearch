<?php namespace Naraki\Elasticsearch\Facades;

use Illuminate\Support\Facades\Facade;

class ElasticSearchIndex extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'elasticsearch.indexer';
    }
}