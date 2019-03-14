<?php namespace Naraki\ElasticSearch\Results;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    /**
     * @var \Naraki\ElasticSearch\Results\SearchResult
     */
    protected $result;

    /**
     *
     * @param \Naraki\ElasticSearch\Results\SearchResult $result
     * @param int $limit
     * @param int $page
     */
    public function __construct(SearchResult $result, int $limit, int $page)
    {
        $this->result = $result;

        parent::__construct($result->hits(), $result->totalHits(), $limit, $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        $hitsReference = &$this->items;

        $result->setHits($hitsReference);
    }

    /**
     *
     * @return \Naraki\ElasticSearch\Results\SearchResult
     */
    public function result(): SearchResult
    {
        return $this->result;
    }
}
