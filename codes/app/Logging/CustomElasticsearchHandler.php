<?php
namespace App\Logging;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Support\Facades\Log;
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

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    protected function write(array $record): void
    {
        $document = [
            'message' => $record['message'],
            'context' => $record['context'] ?? [],
            'level' => $record['level_name'],
            'channel' => $record['channel'],
            'datetime' => $record['datetime']->format('c'), // ISO 8601 format
            'extra' => $record['extra'] ?? [],
            'timestamp' => $record['datetime']->format('Y-m-d\TH:i:s.uP')
        ];

        try {
            $this->client->index([
                'index' => $this->index,
                'body'  => $document,
            ]);
        } catch (\Throwable $exception) {
            Log::channel('stderr')->error('Elasticsearch unavailable', [
                'message' => $exception->getMessage(),
            ]);
        }

    }
}
