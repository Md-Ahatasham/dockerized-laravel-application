<?php
namespace App\Logging;

use Monolog\Formatter\ElasticsearchFormatter;

class CustomElasticsearchFormatter extends ElasticsearchFormatter
{
    public function __construct(string $index, string $type = '_doc')
    {
        // Call the parent constructor with both index and type
        parent::__construct($index, $type);
    }
    public function format(array $record): array
    {
        // Add the timestamp field
        $record['timestamp'] = $record['datetime']->format('Y-m-d\TH:i:s.uP');
        unset($record['datetime']); // Optional: remove the original datetime field

        return parent::format($record);
    }
}
