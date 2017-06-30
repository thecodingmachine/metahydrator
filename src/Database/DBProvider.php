<?php
namespace MetaHydrator\Database;

use MetaHydrator\Exception\DBException;

/**
 * This interface describes a database object access provider. It should allow:
 * - finding a bean in a table, given a set of values and based on the table's primary keys
 * - getting the bean class name corresponding to a table
 */
interface DBProvider
{
    /**
     * This method should:
     * - return null if NO value corresponding to primary keys is passed
     * - throw a DBException if missing some primary keys, or object not found
     * - return the found bean otherwise
     *
     * @param string $table
     * @param array $data
     * @return mixed|null
     *
     * @throws DBException
     */
    public function getObject(string $table, array $data);

    /**
     * This method should return the bean class name corresponding to given table
     *
     * @param string $table
     * @return string
     *
     * @throws DBException
     */
    public function getClassName(string $table);
}
