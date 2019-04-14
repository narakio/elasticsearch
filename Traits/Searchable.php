<?php namespace Naraki\Elasticsearch;

use Naraki\Core\Models\Language;
use Naraki\Elasticsearch\Facades\ElasticSearch;

trait Searchable
{
    /**
     * Are elasticsearch documents associated with this model displayed in a certain language?
     * If so, we've created a locale specific index which is identified using a locale suffix,
     * i.e 'app_name_index_name.en'
     *
     * @var bool
     */
    protected $hasDocumentLocale = true;

    /**
     * @return string
     */
    public function getDocumentType(): string
    {
        return 'main';
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getDocumentIndex($locale = null): string
    {
        if ($this->hasDocumentLocale) {
            return $this->getLocaleDocumentIndex($locale);
        }
        return $this->getDocumentIndexString();
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getLocaleDocumentIndex($locale = null): string
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        } elseif (is_int($locale)) {
            $locale = Language::getLanguageName($locale, false);
        }
        return sprintf('%s.%s', $this->getDocumentIndexString(), $locale);
    }

    /**
     * @return string
     */
    public function getDocumentIndexString(): string
    {
        return strtolower(sprintf('%s.%s', config('app.name'), $this->getTable()));
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method == 'search') {
            //Start an elastic dsl search query builder
            return ElasticSearch::search()->model($this);
        }

        if ($method == 'suggest') {
            //Start an elastic dsl suggest query builder
            return ElasticSearch::suggest()->index($this->getDocumentIndex());
        }

        return parent::__call($method, $parameters);
    }
}
