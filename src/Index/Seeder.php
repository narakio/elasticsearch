<?php namespace Naraki\Elasticsearch\Index;

use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Naraki\Elasticsearch\Results\IndexingResult;

class Seeder
{
    /**
     * @var string
     */
    private $name;

    /**
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param $data
     * @return \Naraki\Elasticsearch\Results\IndexingResult
     */
    public function bulk($data): ?IndexingResult
    {
        $params = ['body' => []];
        $i = 0;
        foreach ($data as $key => $item) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->name,
                    '_type' => 'main',
                    '_id' => $key
                ]
            ];
            $params['body'][] = $item;
            if ($i % 1000 == 0) {
                ElasticSearchIndex::bulk($params);
                $params = ['body' => []];
            }
            $i++;
        }
        if (!empty($params['body'])) {
            $result = ElasticSearchIndex::bulk($params);
            if ($result->hasErrors()) {
                return $result;
            }
        }
        return null;

    }

    public static function delete($index)
    {
        ElasticSearchIndex::delete(['index' => $index]);
    }

    public static function insert($array)
    {
        ElasticSearchIndex::create($array);
    }


}