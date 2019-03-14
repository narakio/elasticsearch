<?php namespace Naraki\ElasticSearch;

/**
 * @method static \Naraki\ElasticSearch\DSL\SearchBuilder search()
 * @method static \Naraki\ElasticSearch\DSL\SuggestionBuilder suggest()
 */
class Manager
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * PlasticManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return \Naraki\ElasticSearch\Connection
     */
    public function connection()
    {
        if (!$this->connection) {
            $this->connection = new Connection($this->config);
        }

        return $this->connection;
    }

    /**
     * @return \Naraki\ElasticSearch\Manager
     */
    public function manager()
    {
        return $this;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
