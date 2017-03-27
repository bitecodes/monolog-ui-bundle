<?php

namespace BiteCodes\MonologUIBundle\Handler;

use BiteCodes\MonologUIBundle\Formatter\NormalizerFormatter;
use BiteCodes\MonologUIBundle\Processor\WebExtendedProcessor;
use Doctrine\DBAL\Connection;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Processor\WebProcessor;

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
     * @var array
     */
    private $logConfig;

    /**
     * @param Connection $connection
     * @param string     $tableName
     * @param array      $logConfig
     * @param int        $level
     * @param boolean    $bubble
     */
    public function __construct(Connection $connection, $tableName, array $logConfig, $level = Logger::DEBUG, $bubble = true)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->logConfig = $logConfig;

        parent::__construct($level, $bubble);

        $this->pushProcessor(new WebProcessor());
        $this->pushProcessor(new WebExtendedProcessor());
    }

    public function isHandling(array $record)
    {
        return $this->isLoggable($record);
    }


    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $record = $record['formatted'];

        unset($record['level_name']);

        try {
            $this->connection->insert($this->tableName, $record);
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

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function isLoggable(array $record)
    {
        if (!isset($record['channel'])) {
            return false;
        }

        if (!isset($this->logConfig[$record['level']])) {
            return false;
        }

        if (is_null($this->logConfig[$record['level']])) {
            return true;
        }

        return in_array($record['channel'], $this->logConfig[$record['level']]);
    }

}
