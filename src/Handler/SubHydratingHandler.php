<?php
namespace MetaHydrator\Handler;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ValidationException;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Reflection\Getter;
use MetaHydrator\Reflection\GetterInterface;
use MetaHydrator\Validator\ValidatorInterface;
use Mouf\Hydrator\Hydrator;

/**
 * An implementation of HydratingHandlerInterface aiming to manage partial edition of sub-objects
 *
 * Class SubHydratingHandler
 * @package MetaHydrator\Handler
 */
class SubHydratingHandler implements HydratingHandlerInterface
{
    /** @var string */
    protected $key;
    public function getKey() { return $this->key; }
    public function setKey(string $key) { $this->key = $key; }

    /** @var string */
    protected $className;
    public function getClassName() { return $this->className; }
    public function setClassName(string $className) { $this->className = $className; }

    /** @var Hydrator */
    protected $hydrator;
    public function getHydrator() { return $this->hydrator; }
    public function setHydrator(Hydrator $hydrator) { $this->hydrator = $hydrator; }

    /** @var ValidatorInterface[] */
    protected $validators;
    public function getValidators() { return $this->validators; }
    public function setValidators(array $validators) { $this->validators = $validators; }
    public function addValidator(ValidatorInterface $validator) { $this->validators[] = $validator; }

    /** @var string */
    protected $errorMessage;
    public function getErrorMessage() { return $this->errorMessage; }
    public function setErrorMessage(string $errorMessage) { $this->errorMessage = $errorMessage; }

    /** @var GetterInterface */
    protected $getter;
    public function getGetter() { return $this->getter; }
    public function setGetter(GetterInterface $getter) { $this->getter = $getter; }

    /**
     * SubHydratingHandler constructor.
     * @param string $key
     * @param string $className
     * @param Hydrator $hydrator
     * @param ValidatorInterface[] $validators
     * @param string $errorMessage
     * @param GetterInterface $getter
     */
    public function __construct(string $key, string $className, Hydrator $hydrator, array $validators = [], string $errorMessage = "", GetterInterface $getter = null)
    {
        $this->key = $key;
        $this->className = $className;
        $this->hydrator = $hydrator;
        $this->validators = $validators;
        $this->errorMessage = $errorMessage;
        $this->getter = $getter ?? new Getter(false);
    }

    /**
     * @param array $data
     * @param array $targetData
     * @param $object
     *
     * @throws HydratingException
     */
    public function handle(array $data, array &$targetData, $object = null)
    {
        if (!array_key_exists($this->key, $data)) {
            if ($object !== null) {
                return;
            } else {
                $subObject = null;
                $targetData[$this->key] = $subObject;
            }
        } elseif ($data[$this->key] === null) {
            $subObject = null;
            $targetData[$this->key] = null;
        } elseif (!is_array($data[$this->key])) {
            throw new HydratingException([$this->key => $this->errorMessage]);
        } else {
            $subData = $data[$this->key];

            try {
                $subObject = $this->getSubObject($object);
                if ($subObject !== null) {
                    $this->hydrator->hydrateObject($subData, $subObject);
                } else {
                    $subObject = $this->hydrator->hydrateNewObject($subData, $this->className);
                    $targetData[$this->key] = $subObject;
                }
            } catch (HydratingException $exception) {
                throw new HydratingException([$this->key => $exception->getErrorsMap()]);
            }
        }

        $this->validate($subObject, $object);
    }

    /**
     * @param mixed $parsedValue
     * @param mixed $contextObject
     *
     * @throws HydratingException
     */
    private function validate($parsedValue, $contextObject = null)
    {
        try {
            foreach ($this->validators as $validator) {
                $validator->validate($parsedValue, $contextObject);
            }
        } catch (ValidationException $exception) {
            throw new HydratingException([ $this->key => $exception->getInnerError() ]);
        }
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function getSubObject($object)
    {
        if ($object === null) {
            return null;
        }
        return $this->getter->get($object, $this->key);
    }
}
