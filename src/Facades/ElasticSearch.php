<?php namespace Naraki\ElasticSearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Naraki\ElasticSearch\DSL\SearchBuilder search()
 * @method static \Naraki\ElasticSearch\DSL\SuggestionBuilder suggest()
 */
class ElasticSearch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'elasticsearch';
    }
}
