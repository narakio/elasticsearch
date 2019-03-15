<?php namespace Naraki\Elasticsearch\Results;

use Illuminate\Support\Collection;

class SearchResult
{
    /**
     * Time needed to execute the query.
     *
     * @var string
     */
    protected $took;

    /**
     * Check if the query timed out.
     *
     * @var boolean
     */
    protected $timed_out;

    /**
     * @var integer
     */
    protected $shards;

    /**
     * Result of the query.
     *
     * @var integer
     */
    protected $hits;

    /**
     * Total number of hits.
     *
     * @var integer
     */
    protected $totalHits;

    /**
     * Highest document score.
     *
     * @var float
     */
    protected $maxScore;

    /**
     * The aggregations result.
     *
     * @var array|null
     */
    protected $aggregations = null;

    /**
     * _construct.
     *
     * @param array $results
     * @param bool $getHits
     */
    public function __construct(array $results, $getHits = true)
    {
        $this->parseResults($results);
        if ($getHits) {
            $this->hits = new Collection($results['hits']['hits']);
        }
    }

    /**
     * @param array $results
     * @return void
     */
    private function parseResults(array $results)
    {
        $this->took = $results['took'];

        $this->timed_out = $results['timed_out'];

        $this->shards = $results['_shards'];

        $this->totalHits = $results['hits']['total'];

        $this->maxScore = $results['hits']['max_score'];

        $this->aggregations = isset($results['aggregations']) ? $results['aggregations'] : [];

    }

    /**
     * @param array $results
     * @param string $model
     * @return \Naraki\Elasticsearch\Results\SearchResult
     */
    public static function toModel(array $results, string $model): self
    {
        $resultSelf = new self($results, false);
        $models = new Collection();
        foreach ($results['hits']['hits'] as $result) {
            $result['_source']['type_id'] = $result['_id'];
            $models->push(new $model($result['_source']));
        }
        $resultSelf->setHits($models);

        return $resultSelf;
    }

    /**
     * @param array $results
     * @return \Naraki\Elasticsearch\Results\SearchResult
     */
    public static function toSource(array $results)
    {
        $resultSelf = new self($results, false);
        $sources = new Collection();
        foreach ($results['hits']['hits'] as $result) {
            $sources->push($result['_source']);
        }
        $resultSelf->setHits($sources);

        return $resultSelf;
    }

    /**
     * Total Hits.
     *
     * @return int
     */
    public function totalHits()
    {
        return $this->totalHits;
    }

    /**
     * Max Score.
     *
     * @return float
     */
    public function maxScore()
    {
        return $this->maxScore;
    }

    /**
     * Get Shards.
     *
     * @return int
     */
    public function shards()
    {
        return $this->shards;
    }

    /**
     * Took.
     *
     * @return string
     */
    public function took()
    {
        return $this->took;
    }

    /**
     * Timed Out.
     *
     * @return bool
     */
    public function timedOut()
    {
        return (bool)$this->timed_out;
    }

    /**
     * Get Hits.
     *
     * Get the hits from Elasticsearch
     * results as a Collection.
     *
     * @return Collection
     */
    public function hits()
    {
        return $this->hits;
    }

    /**
     * @return array
     */
    public function source()
    {
        return $this->hits->pluck('_source')->toArray();

    }

    /**
     * Set the hits value.
     *
     * @param mixed $values
     * @return void
     */
    public function setHits($values)
    {
        $this->hits = $values;
    }

    /**
     * Get aggregations.
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function aggregations()
    {
        return $this->aggregations;
    }

}
