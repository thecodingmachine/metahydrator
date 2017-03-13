<?php
namespace MetaHydrator;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Handler\HydratingHandlerInterface;
use Mouf\Hydrator\Hydrator;
use Mouf\Hydrator\TdbmHydrator;

/**
 * An implementation of Hydrator interface, designed to be configured at will per instance.
 *
 * Class MetaHydrator
 * @package MetaHhydrator
 */
class MetaHydrator implements Hydrator
{
    /** @var HydratingHandlerInterface[] */
    private $handlers = [];

    /** @var Hydrator */
    private $simpleHydrator;

    /**
     * MetaHydrator constructor.
     * @param HydratingHandlerInterface[] $handlers
     * @param Hydrator $simpleHydrator
     */
    public function __construct($handlers = [], $simpleHydrator = null)
    {
        $this->handlers = $handlers;
        $this->simpleHydrator = $simpleHydrator ?? new TdbmHydrator();
    }

    /**
     * @param array $data
     * @param $object
     * @return object
     *
     * @throws HydratingException
     */
    public function hydrateObject(array $data, $object)
    {
        $parsedData = $this->parse($data, $object);
        $this->simpleHydrator->hydrateObject($parsedData, $object);
        return $object;
    }

    /**
     * @param array $data
     * @param string $class
     * @return object
     *
     * @throws HydratingException
     */
    public function hydrateNewObject(array $data, string $className)
    {
        $parsedData = $this->parse($data);
        return $this->simpleHydrator->hydrateNewObject($parsedData, $className);
    }

    /**
     * @param $data
     * @param $contextObject
     * @return array
     *
     * @throws HydratingException
     */
    private function parse($data, $contextObject = null)
    {
        $parsedData = [];
        $errorsMap = [];

        foreach ($this->handlers as $handler) {
            try {
                $handler->handle($data, $parsedData, $contextObject);
            } catch (HydratingException $e) {
                $errorsMap = array_merge($e->getErrorsMap(), $errorsMap);
            }
        }

        if ($errorsMap) {
            throw new HydratingException($errorsMap);
        }

        return $parsedData;
    }
}
