<?php

namespace BiteCodes\MonologUIBundle\Tests\Service;

use BiteCodes\MonologUIBundle\Service\LogConfig;
use Monolog\Logger;

class LogConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @dataProvider records
     *
     * @param $isLoggable
     * @param $record
     */
    public function it_determines_if_a_record_should_be_logged($isLoggable, $record)
    {
        $config = new LogConfig([
            Logger::EMERGENCY => null,
            Logger::ALERT     => null,
            Logger::CRITICAL  => null,
            Logger::ERROR     => null,
            Logger::WARNING   => null,
            Logger::NOTICE    => [],
            Logger::INFO      => ['event'],
            Logger::DEBUG     => [],
        ]);

        $this->assertEquals($isLoggable, $config->isLoggable($record));
    }

    public function records()
    {
        return [
            'missing channel'                    => [false, ['level' => Logger::NOTICE]],
            'missing level'                      => [false, ['channel' => 'event']],
            'unknown level'                      => [false, ['channel' => 'event', 'level' => 42]],
            'log nothing when not in array'      => [false, ['channel' => 'security', 'level' => Logger::INFO]],
            'log nothing when empty array'       => [false, ['channel' => 'event', 'level' => Logger::DEBUG]],
            'log everything when null'           => [true, ['channel' => 'event', 'level' => Logger::EMERGENCY]],
            'log for given level'                => [true, ['channel' => 'event', 'level' => Logger::INFO]],
            'log for level and up (empty array)' => [true, ['channel' => 'event', 'level' => Logger::NOTICE]],
            'log for level and up (null)'        => [true, ['channel' => 'event', 'level' => Logger::WARNING]],
        ];
    }
}
