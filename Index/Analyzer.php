<?php namespace Naraki\Elasticsearch\Index;

use Naraki\Elasticsearch\Exception\InvalidArgumentException;

class Analyzer
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $tokenizer;
    /**
     * @var array
     */
    private $filters;
    /**
     * @var array
     */
    private $charFilters;
    /**
     * @var string
     */
    private $name;

    /**
     *
     * @param $name
     * @param $filters
     * @param $charFilters
     * @param $tokenizer
     * @param $type
     */
    public function __construct(
        string $name,
        array $filters = null,
        array $charFilters = null,
        string $tokenizer = 'standard',
        string $type = 'custom'
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->tokenizer = $tokenizer;
        $this->filters = new Filter($filters);
        $this->charFilters = new CharFilter($charFilters);
    }


    /**
     * @return \Naraki\Elasticsearch\Index\Filter
     */
    public function getFilters(): Filter
    {
        return $this->filters;
    }

    /**
     * @return \Naraki\Elasticsearch\Index\CharFilter
     */
    public function getCharFilters(): CharFilter
    {
        return $this->charFilters;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'tokenizer' => $this->tokenizer,
            'filter' => $this->getFilters()->getNames(),
            'char_filter' => $this->getCharFilters()->getNames()
        ]);
    }
}