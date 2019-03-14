<?php namespace Naraki\Elasticsearch\Contracts;

interface Searchable
{
    public function getDocumentType(): string;

    public function getDocumentIndexString(): string;

    public function getDocumentIndex(): string;

    public function getLocaleDocumentIndex(string $locale = null);

}