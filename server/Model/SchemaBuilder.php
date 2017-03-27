<?php

namespace BiteCodes\MonologUIBundle\Model;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Comparator;

class SchemaBuilder
{
    /**
     * @var Connection $conn
     */
    protected $conn;

    protected $tableName;

    /**
     * @var Schema $schema
     */
    protected $schema;

    public function __construct(Connection $conn, $tableName)
    {
        $this->conn = $conn;
        $this->tableName = $tableName;

        $this->schema = new Schema();

        $entryTable = $this->schema->createTable($this->tableName);
        $entryTable->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $entryTable->addColumn('channel', 'string', ['length' => 255, 'notNull' => true]);
        $entryTable->addColumn('level', 'integer', ['notNull' => true]);
        $entryTable->addColumn('message', 'text', ['notNull' => true]);
        $entryTable->addColumn('datetime', 'datetime', ['notNull' => true]);
        $entryTable->addColumn('context', 'json_array');
        $entryTable->addColumn('extra', 'json_array');
        $entryTable->addColumn('http_server', 'json_array');
        $entryTable->addColumn('http_post', 'json_array');
        $entryTable->addColumn('http_get', 'json_array');
        $entryTable->setPrimaryKey(['id']);
    }

    public function create(\Closure $logger = null)
    {
        $queries = $this->schema->toSql($this->conn->getDatabasePlatform());

        $this->executeQueries($queries, $logger);
    }

    public function update(\Closure $logger = null)
    {
        $queries = $this->getSchemaDiff()->toSaveSql($this->conn->getDatabasePlatform());

        $this->executeQueries($queries, $logger);
    }

    public function getSchemaDiff()
    {
        $diff = new SchemaDiff();
        $comparator = new Comparator();

        $tableDiff = $comparator->diffTable(
            $this->conn->getSchemaManager()->createSchema()->getTable($this->tableName),
            $this->schema->getTable($this->tableName)
        );

        if (false !== $tableDiff) {
            $diff->changedTables[$this->tableName] = $tableDiff;
        }

        return $diff;
    }

    protected function executeQueries(array $queries, \Closure $logger = null)
    {
        $this->conn->beginTransaction();

        try {
            foreach ($queries as $query) {
                if (null !== $logger) {
                    $logger($query);
                }

                $this->conn->query($query);
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}
