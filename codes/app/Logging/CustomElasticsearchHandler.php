<?php
namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Elastic\Elasticsearch\Client;

class CustomElasticsearchHandler extends AbstractProcessingHandler
{
    private $client;
    private $index;

    public function __construct(Client $client, string $index, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->index = $index;
    }

    protected function write(array $record): void
    {
        $document = [
            'message' => $record['message'],
            'context' => $record['context'] ?? [],
            'level' => $record['level_name'],
            'channel' => $record['channel'],
            'datetime' => $record['datetime']->format('c'), // ISO 8601 format
            'extra' => $record['extra'] ?? [],
        ];
        $this->client->index([
            'index' => $this->index,
            'body'  => $document,
        ]);
    }
}
