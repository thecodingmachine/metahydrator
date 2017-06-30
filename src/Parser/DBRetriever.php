<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Database\DBProvider;
use MetaHydrator\Exception\DBException;
use MetaHydrator\Exception\ParsingException;

class DBRetriever extends AbstractParser
{
    /** @var DBProvider */
    protected $dbProvider;
    public function getDbProvider() { return $this->dbProvider; }
    public function setDbProvider(DBProvider $dbProvider) { $this->dbProvider = $dbProvider; }
    /** @var string */
    protected $table;
    public function getTable() { return $this->table; }
    public function setTable(string $table) { $this->table = $table; }

    /**
     * DBRetriever constructor.
     * @param DBProvider $dbProvider
     * @param string $table
     * @param string $errorMessage
     */
    public function __construct(DBProvider $dbProvider, string $table, string $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->dbProvider = $dbProvider;
        $this->table = $table;
    }

    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }
        if (!is_array($rawValue)) {
            throw new ParsingException($this->getErrorMessage());
        }
        try {
            $object = $this->dbProvider->getObject($this->table, $rawValue);
            if ($object === null) {
                throw new ParsingException($this->getErrorMessage());
            } else {
                return $object;
            }
        } catch (DBException $exception) {
            throw new ParsingException($this->getErrorMessage());
        }
    }
}