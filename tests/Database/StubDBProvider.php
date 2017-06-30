<?php
namespace MetaHydratorTest\Database;

use MetaHydrator\Database\DBProvider;
use MetaHydrator\Exception\DBException;

/**
 * A very basic implementation of DBProvider. $db should be an array of tables, each table having of
 * 'pk': array of PK columns
 * 'class': table bean class
 * 'items': beans with ArrayAccess
 */
class StubDBProvider implements DBProvider
{
    public $db;

    public function __construct(array $db)
    {
        $this->db = $db;
    }

    public function addTable(string $table, string $className, array $primaryKeys)
    {
        $this->db[$table] = [
            'class' => $className,
            'pk' => $primaryKeys
        ];
    }

    public function getObject(string $table, array $params)
    {
        if (!isset($this->db[$table])) {
            throw  new DBException('table does not exist');
        }
        return $this->searchObject($this->db[$table], $params);
    }

    private function searchObject($table, $params)
    {
        $pkCount = count(array_intersect($table['pk'], array_keys($params)));
        if ($pkCount == 0) {
            return null;
        } elseif ($pkCount < count($table['pk'])) {
            throw  new DBException('missing primary key values!');
        }
        foreach ($table['items'] as $object) {
            foreach ($table['pk'] as $pk) {
                if ($object[$pk] != $params[$pk]) {
                    continue 2;
                }
            }
            return $object;
        }
        throw  new DBException('not found');
    }

    /**
     * This method should return the bean class name corresponding to given table
     *
     * @param string $table
     * @return string
     *
     * @throws DBException
     */
    public function getClassName(string $table)
    {
        if (!isset($this->db[$table])) {
            throw  new DBException('table does not exist');
        }
        return $this->db[$table]['class'];
    }
}
