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

            $handler = new CustomElasticsearchHandler($client, 'sham_logs');
            $handler->setFormatter(new CustomElasticsearchFormatter('sham_logs', '_doc'));

            $logger->pushHandler($handler);

            return $logger;
        } catch (\Exception $ex) {
            dd('Error:', $ex->getMessage());
        }
    }
}
