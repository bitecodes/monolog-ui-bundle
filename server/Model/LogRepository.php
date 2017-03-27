<?php

namespace BiteCodes\MonologUIBundle\Model;

use Doctrine\DBAL\Connection;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class LogRepository
{
    /**
     * @var Connection $conn
     */
    protected $conn;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @param Connection $conn
     * @param string     $tableName
     */
    public function __construct(Connection $conn, $tableName)
    {
        $this->conn = $conn;
        $this->tableName = $tableName;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->conn->createQueryBuilder();
    }

    /**
     * Initialize a QueryBuilder of latest log entries.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getLogsQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->select('l.channel, l.level, l.message, l.id, l.datetime')
            ->from($this->tableName, 'l')
            ->orderBy('l.datetime', 'DESC');
    }

    /**
     * Initialize a QueryBuilder of latest log entries.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getGroupedLogsQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->select('l.channel, l.level, l.message, MAX(l.id) AS id, MAX(l.datetime) AS datetime, COUNT(l.id) AS count')
            ->from($this->tableName, 'l')
            ->groupBy('l.channel, l.level, l.message')
            ->orderBy('datetime', 'DESC');
    }

    /**
     * Retrieve a log entry by his ID.
     *
     * @param integer $id
     *
     * @return Log|null
     */
    public function getLogById($id)
    {
        $log = $this->createQueryBuilder()
            ->select('l.*')
            ->from($this->tableName, 'l')
            ->where('l.id = :id')
            ->setParameter(':id', $id)
            ->execute()
            ->fetch();

        if (false !== $log) {
            return Log::create($log);
        }
    }

    /**
     * Retrieve last log entry.
     *
     * @return Log|null
     */
    public function getLastLog()
    {
        $log = $this->createQueryBuilder()
            ->select('l.*')
            ->from($this->tableName, 'l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (false !== $log) {
            return new Log($log);
        }
    }

    /**
     * Retrieve similar logs of the given one.
     *
     * @param Log $log
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getSimilarLogsQueryBuilder(Log $log)
    {
        return $this->createQueryBuilder()
            ->select('l.id')
            ->from($this->tableName, 'l')
            ->andWhere('l.message = :message')
            ->andWhere('l.channel = :channel')
            ->andWhere('l.level = :level')
            ->andWhere('l.id != :id')
            ->setParameter(':message', $log->getMessage())
            ->setParameter(':channel', $log->getChannel())
            ->setParameter(':level', $log->getLevel())
            ->setParameter(':id', $log->getId());
    }

    /**
     * @param Log  $log
     * @param bool $withSimilar
     */
    public function removeLog(Log $log, $withSimilar = false)
    {
        if ($withSimilar) {
            $qb = $this->getSimilarLogsQueryBuilder($log);

            $subQuery = $qb->getSQL();

            $qb
                ->resetQueryParts()
                ->delete($this->tableName)
                ->where('id IN (' . $subQuery . ')')
                ->execute();
        }

        $this
            ->createQueryBuilder()
            ->delete($this->tableName)
            ->where('id = :id')
            ->setParameter('id', $log->getId())
            ->execute();
    }

    /**
     * Returns a array of levels with count entries used by logs.
     *
     * @return array
     */
    public function getLogsLevel()
    {
        $levels = $this->createQueryBuilder()
            ->select('l.level, l.level_name, COUNT(l.id) AS count')
            ->from($this->tableName, 'l')
            ->groupBy('l.level, l.level_name')
            ->orderBy('l.level', 'DESC')
            ->execute()
            ->fetchAll();

        $normalizedLevels = [];
        foreach ($levels as $level) {
            $normalizedLevels[$level['level']] = sprintf('%s (%s)', $level['level_name'], $level['count']);
        }

        return $normalizedLevels;
    }
}