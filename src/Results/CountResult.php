<?php namespace Naraki\Elasticsearch\Results;

class CountResult
{
    /**
     * @var int
     */
    protected $count;
    /**
     * @var array
     */
    protected $shards;

    /**
     *
     */
    public function __construct(array $results)
    {
        $this->parseResults($results);
    }

    public function parseResults($results)
    {
        $this->count = $results['count'];
        $this->shards = $results['_shards'];
    }

    public function count()
    {
        return $this->count;
    }

    public function shards()
    {
        return $this->shards;
    }

}