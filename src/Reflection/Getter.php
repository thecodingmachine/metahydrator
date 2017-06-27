<?php
namespace MetaHydrator\Reflection;

use ArrayAccess;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Getter implements GetterInterface
{
    /** @var bool */
    protected $throwOnFail;

    /** @var bool */
    protected $ignoreProtected;

    /**
     * Getter constructor.
     * @param bool $throwOnFail
     * @param bool $ignoreProtected
     */
    public function __construct(bool $throwOnFail = true, bool $ignoreProtected = false)
    {
        $this->throwOnFail = $throwOnFail;
        $this->ignoreProtected = $ignoreProtected;
    }

    /**
     * @param $object
     * @param string $field
     * @return mixed
     */
    public function get($object, string $field)
    {
        if (is_array($object)) {
            return $this->getFromArray($object, $field);
        }

        if (is_object($object)) {
            return $this->getFromObject($object, $field);
        }

        if ($this->throwOnFail) {
            throw new ReflectionException("cannot extract field '$field' from non-object, non-array value");
        } else {
            return null;
        }
    }

    /**
     * @param $object
     * @param string $field
     * @return null
     * @throws ReflectionException
     */
    protected function getFromArray($object, string $field)
    {
        if (array_key_exists($field, $object)) {
            return $object[$field];
        } else if ($this->throwOnFail) {
            throw new ReflectionException("cannot extract field '$field' from array");
        } else {
            return null;
        }
    }

    /**
     * @param $object
     * @param string $field
     * @return mixed|null
     * @throws ReflectionException
     */
    protected function getFromObject($object, string $field)
    {
        $reflectionClass = new ReflectionClass($object);

        $getterMethod = $this->findGetterMethod($reflectionClass, $field);
        if ($getterMethod !== null) {
            return $getterMethod->invoke($object);
        }

        $property = $this->findProperty($reflectionClass, $field);
        if ($property !== null) {
            return $property->getValue($object);
        }

        if ($object instanceof ArrayAccess && $object->offsetExists($field)) {
            return $object->offsetGet($field);
        }

        if ($this->throwOnFail) {
            throw new ReflectionException("cannot extract field '$field' from object");
        } else {
            return null;
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $field
     * @return null|ReflectionMethod
     */
    protected function findGetterMethod(ReflectionClass $reflectionClass, string $field)
    {
        foreach ($reflectionClass->getMethods($this->ignoreProtected ? ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_PROTECTED|ReflectionMethod::IS_PRIVATE : ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()
                && $reflectionMethod->getNumberOfRequiredParameters() == 0
                && strcasecmp($reflectionMethod->getName(), "get$field") == 0) {
                if (!$reflectionMethod->isPublic()) {
                    $reflectionMethod->setAccessible(true);
                }
                return $reflectionMethod;
            }
        }
        return null;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $field
     * @return null|\ReflectionProperty
     */
    protected function findProperty(ReflectionClass $reflectionClass, string $field)
    {
        if ($reflectionClass->hasProperty($field)) {
            $property = $reflectionClass->getProperty($field);
            if (!$property->isStatic()) {
                if ($property->isPublic()) {
                    return $property;
                } else if ($this->ignoreProtected) {
                    $property->setAccessible(true);
                    return $property;
                }
            }
        }
        return null;
    }
}
