<?php

namespace BiteCodes\MonologUIBundle\Service;

class LogConfig
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    public function isLoggable(array $record)
    {
        if ($this->channelMissing($record) || $this->levelMissing($record)) {
            return false;
        }

        if ($this->unknownLevel($record)) {
            return false;
        }

        if ($this->levelLogsAll($record)) {
            return true;
        }

        return $this->levelHandlesChannel($record);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function channelMissing(array $record)
    {
        return !isset($record['channel']);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function levelMissing(array $record)
    {
        return !isset($record['level']);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function unknownLevel(array $record)
    {
        return !array_key_exists($record['level'], $this->config);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function levelLogsAll(array $record)
    {
        return is_null($this->config[$record['level']]);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function levelHandlesChannel(array $record)
    {
        return count(array_filter($this->config, function ($channels, $level) use ($record) {
            return $level >= $record['level'] && in_array($record['channel'], is_array($channels) ? $channels : []);
        }, ARRAY_FILTER_USE_BOTH)) > 0;
    }
}
