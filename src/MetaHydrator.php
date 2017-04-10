<?php
namespace MetaHydrator;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ValidationException;
use MetaHydrator\Handler\HydratingHandlerInterface;
use MetaHydrator\Validator\ValidatorInterface;
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

    /** @var ValidatorInterface[] */
    private $validators = [];

    /** @var Hydrator */
    private $simpleHydrator;

    /**
     * MetaHydrator constructor.
     * @param HydratingHandlerInterface[] $handlers
     * @param ValidatorInterface[] $validators
     * @param Hydrator $simpleHydrator
     */
    public function __construct($handlers = [], $validators = [], $simpleHydrator = null)
    {
        $this->handlers = $handlers;
        $this->validators = $validators;
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
     * @param string $className
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

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($parsedData, $contextObject);
            } catch (ValidationException $e) {
                $errorsMap = array_merge($e->getInnerError(), $errorsMap);
            }
        }

        if (!empty($errorsMap)) {
            throw new HydratingException($errorsMap);
        }

        return $parsedData;
    }
}
