<?php namespace Naraki\ElasticSearch\Index;

class Mapping
{
    /**.
     * @var string
     */
    private $indexName;
    /**
     * @var array
     */
    private $analysis;
    /**
     * @var array
     */
    private $mappings;
    /**
     * @var array
     */
    private $source;

    /**
     *
     * @param string $name
     * @param array $mappings
     * @param array $source
     */
    public function __construct(string $name, array $mappings = null, $source = null)
    {
        $this->indexName = $name;
        $this->mappings = $mappings;
        $this->source = $source;
        $this->analysis = new Analysis($this->getAnalyzers($mappings));
    }

    public function toArray()
    {
        return [
            'index' => $this->indexName,
            'body' => array_filter([
                'settings' => array_filter([
                    'analysis' => $this->analysis->toArray()
                ]),
                'mappings' => [
                    'main' => array_filter([
                        '_source' => $this->source,
                        'properties' =>
                            $this->mappings
                    ])
                ]
            ])
        ];
    }

    private function getAnalyzers($mappings)
    {
        $analyzers = [];
        foreach ($mappings as $mappingType => $mapping) {
            if (is_array($mapping)) {
                $analyzers = array_merge($analyzers, $this->getAnalyzers($mapping));
            } else {
                if (strpos($mappingType, 'analyzer') !== false) {
                    $analyzers[] = $mapping;
                }
            }
        }
        return $analyzers;
    }

    public function setMappingValues($index, $values)
    {
        if (isset($this->mappings[$index])) {
            foreach ($values as $idx => $value) {
                $this->mappings[$index][$idx] = $value;
            }
        }
    }

}