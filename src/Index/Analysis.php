<?php namespace Naraki\Elasticsearch\Index;

use Naraki\Elasticsearch\Exception\InvalidArgumentException;

class Analysis
{

    /**
     * @var array
     */
    private $analyzers = [];
    private $filters = [];
    private $charFilters = [];
    /**
     * @var array
     */
    private $builtInAnalyzers = [
        'standard',
        'simple',
        'whitespace',
        'stop',
        'keyword',
        'pattern',
        'fingerprint',
    ];

    /**
     * Custom analyzers are defined here.
     *
     * Analyzer definition is read as a flat array by the Analyzer constructor which expects params in a certain order:
     * First are filters,
     * then Character filters
     * then Tokenizer
     * then Type
     *
     *
     * @var array
     */
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
        ],
        'std_autocomplete_string' => [
            [
                'lowercase',
                'autocomplete_string'
            ]
        ],
        'std_autocomplete_slug' => [
            [
                'word_delimiter',
                'autocomplete_string',
            ]
        ],
    ];

    /**
     *
     * @param array $analyzerList
     */
    public function __construct(
        $analyzerList
    ) {
        if (is_null($analyzerList)) {
            return null;
        }
        foreach ($analyzerList as $item) {
            if (isset($this->availableAnalyzers[$item])) {
                $analyzer = new Analyzer($item, ... $this->availableAnalyzers[$item]);
                $this->analyzers[$item] = $analyzer->toArray();
                $this->filters = array_merge($this->filters, $analyzer->getFilters()->filters());
                $this->charFilters = array_merge($this->charFilters, $analyzer->getCharFilters()->charFilters());
            } else {
                if (!$this->exists($item)) {
                    throw new InvalidArgumentException(sprintf('Analyzer %s was not found', $item));
                }
            }
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'analyzer' => $this->analyzers,
            'filter' => array_filter($this->filters),
            'char_filter' => array_filter($this->charFilters)
        ]);
    }

    /**
     * @param string $analyzer
     * @return bool
     */
    private function exists(string $analyzer): bool
    {
        $builtIn = array_flip($this->builtInAnalyzers);
        return isset($builtIn[$analyzer]);
    }


}