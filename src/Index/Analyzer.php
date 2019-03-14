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
     * @var array
     */
    private static $builtInAnalyzers = [
        'standard',
        'simple',
        'whitespace',
        'stop',
        'keyword',
        'pattern',
        'fingerprint',
    ];

    private $availableAnalyzers = [
        'std_strip_en' => [
            [
                'lowercase',
                'stop_en',
                'snowball_en',
            ],
            [
                'html_strip',
                'quotes'
            ]
        ],
        'std_strip_fr' => [
            [
                'lowercase',
                'stop_fr',
                'snowball_fr',
                'asciifolding'
            ],
            [
                'html_strip',
                'quotes'
            ]
        ],
        'std_stop_en' => [
            [
                'lowercase',
                'stop_en',
                'snowball_en',
            ]
        ],
        'std_stop_fr' => [
            [
                'lowercase',
                'stop_fr',
                'snowball_fr',
                'asciifolding'
            ]
        ]
    ];

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



    /**
     * @param string $analyzer
     * @return bool
     */
    public static function exists(string $analyzer): bool
    {
        $builtIn = array_flip(self::$builtInAnalyzers);
        return isset($builtIn[$analyzer]);

    }


}