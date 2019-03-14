<?php namespace Naraki\Elasticsearch\Index;

class Filter
{
    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var array
     */
    private $availableFilters = [
        'stop_en' => [
            'type' => 'stop',
            'stopwords' => '_english_'
        ],
        'stop_fr' => [
            'type' => 'stop',
            'stopwords' => '_french_'
        ],
        'snowball_en' => [
            'type' => 'snowball',
            'language' => 'English'
        ],
        'snowball_fr' => [
            'type' => 'snowball',
            'language' => 'French'
        ],
    ];

    /**
     * @param array $filterList
     */
    public function __construct(array $filterList = null)
    {
        if (is_null($filterList)) {
            return null;
        }
        foreach ($filterList as $filterName) {
            if (isset($this->availableFilters[$filterName])) {
                $this->filters[$filterName] = $this->availableFilters[$filterName];
            } else {
                $this->filters[$filterName] = null;
            }
        }
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->filters);
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return $this->filters;
    }

}