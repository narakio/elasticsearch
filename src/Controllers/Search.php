<?php namespace Naraki\Elasticsearch\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class Search extends Controller
{
    /**
     * @var \Naraki\Elasticsearch\Manager
     */
    private $es;

    public function __construct()
    {
        $this->es = app('elasticsearch');
    }

    /**
     * @param string $source
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function post($source=null)
    {
//        throw new \Exception('ddd');
        $input = app('request')->get('q');
        if (!is_null($input && !empty($input))) {
            list($blog, $author, $tag) = [
                is_null($source)?$this->searchBlog($input):$this->searchBlogPaginate($input),
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
     * @return array
     */
    private function searchBlog($input, $size = 4)
    {
        return $this->es->search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->from(0)
            ->size($size)
            ->matchPhrasePrefix('title', strip_tags($input))->get()->source();
    }

    /**
     * @param string $input
     * @param int $size
     * @return \Naraki\Elasticsearch\Results\Paginator
     */
    private function searchBlogPaginate($input, $size=8)
    {
        return $this->es->search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->matchPhrasePrefix('title', strip_tags($input))->paginate($size,false);

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
}