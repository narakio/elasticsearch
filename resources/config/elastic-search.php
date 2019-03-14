<?php

return [
    /*
     * Connection settings
     */
    'connection' => [

        /*
        |--------------------------------------------------------------------------
        | Hosts
        |--------------------------------------------------------------------------
        |
        | The most common configuration is telling the client about your cluster: how many nodes, their addresses and ports.
        | If no hosts are specified, the client will attempt to connect to localhost:9200.
        |
        */
        'hosts' => [
            env('ELASTIC_SEARCH_HOST', '127.0.0.1:9200'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Reties
        |--------------------------------------------------------------------------
        |
        | By default, the client will retry n times, where n = number of nodes in your cluster.
        | A retry is only performed if the operation results in a "hard" exception.
        |
        */
        'retries' => env('ELASTIC_SEARCH_RETRIES', 3),

        /*
        |------------------------------------------------------------------
        | Logging
        |------------------------------------------------------------------
        |
        | Logging is disabled by default for performance reasons. The recommended logger is Monolog (used by Laravel),
        | but any logger that implements the PSR/Log interface will work.
        |
        | @more https://www.elastic.co/guide/en/elasticsearch/client/php-api/6.0/_configuration.html#enabling_logger
        |
        */
        'logging' => [
            'enabled' => env('ELASTIC_SEARCH_LOG', false),
            'path' => storage_path(env('ELASTIC_SEARCH_LOG_PATH', 'logs/elastic.log')),
            'level' => env('ELASTIC_SEARCH_LOG_LEVEL', 200),
        ],
    ],

];
