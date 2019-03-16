<?php namespace Naraki\Elasticsearch\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Naraki\Elasticsearch\DSL\SearchBuilder;

class Search extends Controller
{
    /**
     * @var \Naraki\Elasticsearch\Manager
     */
    private $es;
    /**
     * @var int
     */
    private $size;

    public function __construct()
    {
        $this->es = app('elasticsearch');
    }

    /**
     * @param string $source
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function post($source = null)
    {
        $input = app('request')->get('q');
        $size = app('request')->get('size');
        $sort = app('request')->get('sort');
        $order = app('request')->get('order');
        if (!is_null($input && !empty($input))) {
            list($blog, $author, $tag) = [
                is_null($source)
                    ? $this->searchBlog(
                    $input,
                    (!is_null($size) && intval($size) <= 20)
                        ? intval($size)
                        : 4,
                    $sort,
                    $order)
                    : $this->searchBlogPaginate($input, $sort, $order),
                $this->searchAuthor($input),
                $this->searchTag($input)
            ];
            return response([
                'status' => 'ok',
                'articles' => $blog,
                'authors' => $author,
                'tags' => $tag
            ], Response::HTTP_OK);
        }
        return response(['status' => null, Response::HTTP_OK]);
    }

    /**
     * @param string $input
     * @param int $size
     * @param string $sort
     * @param string $order
     * @return array
     */
    private function searchBlog($input, $size, $sort, $order)
    {
        $search = $this->es->search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->from(0)
            ->size($size)
            ->matchPhrasePrefix('title', strip_tags($input));
        return $this->sort($search, $sort, $order)->get()->source();
    }

    /**
     * @param string $input
     * @param string $sort
     * @param string $order
     * @return \Naraki\Elasticsearch\Results\Paginator
     */
    private function searchBlogPaginate($input, $sort, $order)
    {
        $search = $this->es->search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->matchPhrasePrefix('title', strip_tags($input));
        return $this->sort($search, $sort, $order)->paginateToSource(8);
    }

    /**
     * @param string $input
     * @param int $size
     * @return array
     */
    private function searchAuthor($input, $size = 4)
    {
        return $this->es->search()
            ->index('naraki.blog_authors.en')
            ->from(0)
            ->size($size)
            ->type('main')
            ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
    }

    /**
     * @param string $input
     * @param int $size
     * @return array
     */
    private function searchTag($input, $size = 4)
    {
        return $this->es->search()
            ->index('naraki.blog_tags.en')
            ->type('main')
            ->from(0)
            ->size($size)
            ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
    }

    /**
     * @param \Naraki\Elasticsearch\DSL\SearchBuilder $searchObject
     * @param string $field
     * @param string $order
     * @return mixed
     */
    private function sort($searchObject, $field, $order): SearchBuilder
    {
        $sortOptions = ['date' => true];
        if (!is_null($field) && isset($sortOptions[$field])) {
            return $searchObject->sortBy(
                $field,
                (!is_null($order) && strpos($order, 'asc,desc') !== false) ? $order : 'desc'
            );
        }
        return $searchObject;
    }
}