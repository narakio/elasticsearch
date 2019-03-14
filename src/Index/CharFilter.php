<?php namespace Naraki\ElasticSearch\Index;

class CharFilter
{
    /**
     * @var array
     */
    private $charFilters = [];

    /**
     * @var array
     */
    private $availableFilters = [
        'quotes' => [
            'type' => 'mapping',
            'mappings' => [
                '\\u0091=>\\u0027',
                '\\u0092=>\\u0027',
                '\\u2018=>\\u0027',
                '\\u2019=>\\u0027',
                '\\u201B=>\\u0027',
                '\\u201C=>\\u0022',
                '\\u201D=>\\u0022',
                '\\u00AB=>\\u0022',
                '\\u00BB=>\\u0022',
            ]
        ]
    ];

    /**
     * @param array $charFilterList
     */
    public function __construct(array $charFilterList = null)
    {
        if (is_null($charFilterList)) {
            return null;
        }
        $this->charFilters = [];
        foreach ($charFilterList as $charFilter) {
            if (isset($this->availableFilters[$charFilter])) {
                $this->charFilters[$charFilter] = $this->availableFilters[$charFilter];
            } else {
                $this->charFilters[$charFilter] = null;
            }
        }
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->charFilters);
    }

    public function charFilters(): array
    {
        return $this->charFilters;
    }


}