<?php

namespace BiteCodes\MonologUIBundle\Handler;

use BiteCodes\MonologUIBundle\Service\LogConfig;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DoctrineDBALHandler extends AbstractProcessingHandler
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var string $tableName
     */
    private $tableName;
    /**
     * @var LogConfig
     */
    private $logConfig;

    /**
     * @param Connection $connection
     * @param string     $tableName
     * @param LogConfig  $logConfig
     * @param int        $level
     * @param boolean    $bubble
     */
    public function __construct(
        Connection $connection,
        $tableName,
        LogConfig $logConfig,
        $level = Logger::DEBUG,
        $bubble = true
    )
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->logConfig = $logConfig;

        parent::__construct($level, $bubble);
    }

    public function isHandling(array $record)
    {
        return $this->logConfig->isLoggable($record);
    }


    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $record = $record['formatted'];

        // no need to be stored
        unset($record['level_name']);

        try {
            $this->connection->insert($this->tableName, $record, [
                'context'     => Type::JSON_ARRAY,
                'extra'       => Type::JSON_ARRAY,
                'http_server' => Type::JSON_ARRAY,
                'http_get'    => Type::JSON_ARRAY,
                'http_post'   => Type::JSON_ARRAY,
            ]);
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        return new NormalizerFormatter('U');
    }
}
