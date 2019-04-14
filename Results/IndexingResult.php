<?php namespace Naraki\Elasticsearch\Results;

use Illuminate\Support\Collection;

class IndexingResult
{
    /**
     * @var string
     */
    protected $took;

    /**
     * @var boolean
     */
    protected $errors;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     *
     * @param array $results
     */
    public function __construct(array $results)
    {
        $this->took = format_duration($results['took']);
        $this->errors = $results['errors'];
        $this->items = new Collection($results['items']);
    }

    public function hasErrors()
    {
        return $this->errors;
    }
}