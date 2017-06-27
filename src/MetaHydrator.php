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
    private $handlers;
    public function getHandlers() { return $this->handlers; }
    public function setHandlers(array $handlers) { $this->handlers = $handlers; }
    public function addHandler(HydratingHandlerInterface $handler) { $this->handlers[] = $handler; }

    /** @var array<string,ValidatorInterface> */
    private $validators;
    public function getValidators() { return $this->validators; }
    public function setvalidators(array $validators) { $this->validators = $validators; }
    public function addValidator(string $key, ValidatorInterface $validator) { $this->validators[$key] = $validator; }

    /** @var Hydrator */
    private $simpleHydrator;
    public function getSimpleHydrator() { return $this->simpleHydrator; }
    public function setSimpleHydrator(Hydrator $hydrator) { $this->simpleHydrator = $hydrator; }

    /**
     * MetaHydrator constructor.
     * @param HydratingHandlerInterface[] $handlers
     * @param array<string,ValidatorInterface> $validators
     * @param Hydrator $simpleHydrator
     */
    public function __construct(array $handlers = [], array $validators = [], Hydrator $simpleHydrator = null)
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

        foreach ($this->validators as $key => $validator) {
            try {
                $validator->validate($parsedData, $contextObject);
            } catch (ValidationException $e) {
                $errorsMap = array_merge([$key => $e->getInnerError()], $errorsMap);
            }
        }

        if (!empty($errorsMap)) {
            throw new HydratingException($errorsMap);
        }

        return $parsedData;
    }
}
