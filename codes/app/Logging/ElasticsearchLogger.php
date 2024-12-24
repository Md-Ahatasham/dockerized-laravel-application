<?php

namespace App\Logging;

//use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\ClientBuilder;
//use Elasticsearch\ClientBuilder;
use Monolog\Logger;
use Monolog\Handler\ElasticSearchHandler;

class ElasticsearchLogger
{
    public function __invoke(array $config)
    {
        try {
            $logger = new Logger('elasticsearch');
            $client = ClientBuilder::create()
                ->setHosts(['http://172.23.0.1:9200'])
                ->build();

            $options = [
                'index' => 'user_logs', // Ensure this index exists
                'type'  => '_doc',      // Required for Elasticsearch 7.x
            ];
            $handler = new CustomElasticsearchHandler($client, 'user_logs');
//
//            try {
//                $handler = new CustomElasticsearchHandler($client, 'user_logs');
//                dd('sdf',$handler);
//            } catch (\Exception $exception) {
//                dd($exception->getMessage());
//            }

//
            $logger->pushHandler($handler);

            return $logger;
        } catch (\Exception $ex) {
            dd('Error:', $ex->getMessage());
        }
    }
}
