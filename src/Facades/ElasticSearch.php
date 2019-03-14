<?php namespace Naraki\Elasticsearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Naraki\Elasticsearch\DSL\SearchBuilder search()
 * @method static \Naraki\Elasticsearch\DSL\SuggestionBuilder suggest()
 */
class ElasticSearch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'elasticsearch';
    }
}
