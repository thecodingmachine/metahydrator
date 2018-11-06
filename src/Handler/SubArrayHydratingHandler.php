<?php
namespace MetaHydrator\Handler;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ValidationException;
use MetaHydrator\Reflection\Getter;
use MetaHydrator\Reflection\GetterInterface;
use MetaHydrator\Validator\ValidatorInterface;
use Mouf\Hydrator\Hydrator;

/**
 * Class SubArrayHydratingHandler
 * @package MetaHydrator\Handler
 */
class SubArrayHydratingHandler implements HydratingHandlerInterface
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

    /** @var string */
    protected $errorMessage;
    public function getErrorMessage() { return $this->errorMessage; }
    public function setErrorMessage(string $errorMessage) { $this->errorMessage = $errorMessage; }

    /** @var string */
    private $subErrorMessage;
    public function getSubErrorMessage() { return $this->subErrorMessage; }
    public function setSubErrorMessage($subErrorMessage) { $this->subErrorMessage = $subErrorMessage; }

    /** @var ValidatorInterface[] */
    private $validators;
    public function getValidators() { return $this->validators; }
    public function setValidators(array $validators) { $this->validators = $validators; }
    public function addValidator(ValidatorInterface $validator) { $this->validators[] = $validator; }

    /** @var GetterInterface */
    protected $getter;
    public function getGetter() { return $this->getter; }
    public function setGetter(GetterInterface $getter) { $this->getter = $getter; }

    /** @var bool */
    private $associative;
    public function getAssociative() { return $this->associative; }
    public function setAssociative(bool $associative) { $this->associative = $associative; }

    /**
     * SubArrayHydratingHandler constructor.
     * @param string $key
     * @param string $className
     * @param Hydrator $hydrator
     * @param ValidatorInterface[] $validators
     * @param string $errorMessage
     * @param string $subErrorMessage
     * @param GetterInterface $getter
     * @param bool $associative
     */
    public function __construct(string $key, string $className, Hydrator $hydrator, array $validators = [], string $errorMessage = "", string $subErrorMessage = "", GetterInterface $getter = null, bool $associative = false)
    {
        $this->key = $key;
        $this->className = $className;
        $this->hydrator = $hydrator;
        $this->validators = $validators;
        $this->errorMessage = $errorMessage;
        $this->subErrorMessage = $subErrorMessage;
        $this->getter = $getter ?? new Getter(false);
        $this->associative = $associative;
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
                $subArray = null;
            }
        } elseif ($data[$this->key] === null) {
            $subArray = null;
        } elseif (!is_array($data[$this->key])) {
            throw new HydratingException([$this->key => $this->errorMessage]);
        } else {
            $subArrayData = $data[$this->key];
            $subArray = $this->getSubArray($object) ?? null;
            $errorsMap = [];
            $ok = true;
            foreach ($subArrayData as $key => $subData) {
                $ok = $this->handleSub($key, $subData, $subArray, $errorsMap) && $ok;
            }
            if (!$ok) {
                throw new HydratingException([$this->key => $errorsMap]);
            }
        }

        $this->validate($subArray, $object);

        if ($this->associative) {
            $targetData[$this->key] = $subArray;
        } elseif ($subArray !== null) {
            $targetData[$this->key] = array_values($subArray);
        } else {
            $targetData[$this->key] = null;
        }
    }

    /**
     * @param string $key
     * @param array|null $data
     * @param array $array
     * @param array $errorsMap
     * @return bool
     */
    protected function handleSub($key, $data, &$array, &$errorsMap)
    {
        if ($data === null) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
            $errorsMap[$key] = null;
            return true;
        }
        if (!is_array($data)) {
            $errorsMap[$key] = $this->subErrorMessage;
            return false;
        }
        if (isset($array[$key])) {
            $subObject = $array[$key];
            try {
                $this->hydrator->hydrateObject($data, $subObject);
                $errorsMap[$key] = null;
                return true;
            } catch (HydratingException $exception) {
                $errorsMap[$key] = $exception->getErrorsMap();
                return false;
            }
        } else {
            try {
                $subObject = $this->hydrator->hydrateNewObject($data, $this->className);
                $array[$key] = $subObject;
                $errorsMap[$key] = null;
                return true;
            } catch (HydratingException $exception) {
                $errorsMap[$key] = $exception->getErrorsMap();
                return false;
            }
        }
    }

    /**
     * @param $object
     * @return array
     */
    protected function getSubArray($object)
    {
        if ($object === null) {
            return [];
        }
        return $this->getter->get($object, $this->key);
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
}
