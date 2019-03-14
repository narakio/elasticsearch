<?php

namespace Naraki\ElasticSearch;

use Naraki\ElasticSearch\DSL\SearchBuilder;
use Naraki\ElasticSearch\DSL\SuggestionBuilder;
use Naraki\ElasticSearch\Index\Builder as IndexBuilder;
use Naraki\ElasticSearch\Persistence\EloquentPersistence;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Endpoints\Suggest;
use ONGR\ElasticsearchDSL\Search as DSLQuery;

class Connection
{
    /**
     * Elastic Search default index.
     *
     * @var string
     */
    protected $index;

    /**
     * Elastic search client instance.
     *
     * @var Client
     */
    protected $elastic;

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->elastic = $this->buildClient($config['connection']);
    }

    /**
     * @return \Naraki\ElasticSearch\Index\Builder
     */
    public function getIndexBuilder()
    {
        return new IndexBuilder($this);
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * Get DSL grammar instance for this connection.
     *
     * @return \ONGR\ElasticsearchDSL\Search
     */
    public function getDSLQuery()
    {
        return new DSLQuery();
    }

    /**
     * Get the elastic search client instance.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->elastic;
    }

    /**
     * Set a custom elastic client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->elastic = $client;
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $search
     *
     * @return array
     */
    public function searchStatement(array $search)
    {
        return $this->elastic->search($this->setStatementIndex($search));
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $suggestions
     *
     * @return array
     */
    public function suggestStatement(array $suggestions)
    {
        return $this->elastic->suggest($this->setStatementIndex($suggestions));
    }

    /**
     * Execute a insert statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function indicesCreateStatement(array $params)
    {
        return $this->elastic->indices()->create($params);
    }

    /**
     * Execute a insert statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function indicesDeleteStatement(array $params)
    {
        if ($this->elastic->indices()->exists($params)) {
            return $this->elastic->indices()->delete($params);
        }
        return [];
    }

    /**
     * Execute a insert statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function indexStatement(array $params)
    {
        return $this->elastic->index($this->setStatementIndex($params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function updateStatement(array $params)
    {
        return $this->elastic->update($this->setStatementIndex($params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function deleteStatement(array $params)
    {
        return $this->elastic->delete($this->setStatementIndex($params));
    }

    /**
     * Execute a exists statement on index.
     *
     * @param array $params
     *
     * @return array|bool
     */
    public function existsStatement(array $params)
    {
        return $this->elastic->exists($this->setStatementIndex($params));
    }

    /**
     * Execute a bulk statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function bulkStatement(array $params)
    {
        return $this->elastic->bulk($params);
    }

    /**
     * Begin a fluent search query builder.
     *
     * @return SearchBuilder
     */
    public function search(): SearchBuilder
    {
        return new SearchBuilder($this, $this->getDSLQuery());
    }

    /**
     * Begin a fluent suggest query builder.
     *
     * @return SuggestionBuilder
     */
    public function suggest(): SuggestionBuilder
    {
        return new SuggestionBuilder($this, $this->getDSLQuery());
    }

    /**
     * Create a new elastic persistence handler.
     *
     * @return EloquentPersistence
     */
    public function persist()
    {
        return new EloquentPersistence($this);
    }

    /**
     * Create an elastic search instance.
     *
     * @param array $config
     *
     * @return Client
     */
    private function buildClient(array $config)
    {
        $client = ClientBuilder::create()
            ->setHosts($config['hosts']);

        if (isset($config['retries'])) {
            $client->setRetries($config['retries']);
        }

        if (isset($config['logging']) and $config['logging']['enabled'] == true) {
            $logger = ClientBuilder::defaultLogger($config['logging']['path'], $config['logging']['level']);
            $client->setLogger($logger);
        }

        return $client->build();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function setStatementIndex(array $params)
    {
        if (isset($params['index'])) {
            return $params;
        }

        $params['index'] = $this->index;
        return $params;
    }
}
