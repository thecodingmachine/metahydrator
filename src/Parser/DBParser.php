<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Database\DBProvider;
use MetaHydrator\Exception\DBException;
use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ParsingException;
use Mouf\Hydrator\Hydrator;

class DBParser extends AbstractParser
{
    /** @var DBProvider */
    protected $dbProvider;
    public function getDbProvider() { return $this->dbProvider; }
    public function setDbProvider(DBProvider $dbProvider) { $this->dbProvider = $dbProvider; }
    /** @var string */
    protected $table;
    public function getTable() { return $this->table; }
    public function setTable(string $table) { $this->table = $table; }
    /** @var Hydrator */
    private $hydrator;
    public function getHydrator() { return $this->hydrator; }
    public function setHydrator(Hydrator $hydrator) { $this->hydrator = $hydrator; }

    /**
     * DBParser constructor.
     * @param DBProvider $dbProvider
     * @param string $table
     * @param Hydrator $hydrator
     * @param string $errorMessage
     */
    public function __construct(DBProvider $dbProvider, string $table, Hydrator $hydrator, string $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->dbProvider = $dbProvider;
        $this->table = $table;
        $this->hydrator = $hydrator;
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
        } catch (DBException $exception) {
            throw new ParsingException($this->getErrorMessage());
        }
        try {
            if ($object === null) {
                $className = $this->dbProvider->getClassName($this->table);
                return $this->hydrator->hydrateNewObject($rawValue, $className);
            } else {
                $this->hydrator->hydrateObject($rawValue, $object);
                return $object;
            }
        } catch (HydratingException $exception) {
            throw new ParsingException($exception->getErrorsMap());
        }
    }
}