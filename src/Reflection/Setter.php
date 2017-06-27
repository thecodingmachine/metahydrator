<?php
namespace MetaHydrator\Reflection;

use ArrayAccess;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Setter implements SetterInterface
{
    /** @var bool */
    protected $throwOnFail;

    /** @var bool */
    protected $ignoreProtected;

    /**
     * Setter constructor.
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
     * @param mixed $value
     */
    public function set(&$object, string $field, $value)
    {
        if (is_array($object)) {
            $this->setInArray($object, $field, $value);
            return;
        }

        if (is_object($object)) {
            $this->setInObject($object, $field, $value);
            return;
        }

        if ($this->throwOnFail) {
            throw new ReflectionException("cannot set field '$field' in non-object, non-array value");
        }
    }

    protected function setInArray(&$object, string $field, $value)
    {
        $object[$field] = $value;
    }

    protected function setInObject(&$object, string $field, $value)
    {
        $reflectionClass = new ReflectionClass($object);

        $setterMethod = $this->findSetterMethod($reflectionClass, $field);
        if ($setterMethod !== null) {
            $setterMethod->invoke($object, $value);
            return;
        }

        $property = $this->findProperty($reflectionClass, $field);
        if ($property !== null) {
            $property->setValue($object, $value);
            return;
        }

        if ($object instanceof ArrayAccess) {
            $object->offsetSet($field, $value);
            return;
        }

        if ($this->throwOnFail) {
            throw new ReflectionException("cannot set field '$field' in object");
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $field
     * @return null|ReflectionMethod
     */
    protected function findSetterMethod(ReflectionClass $reflectionClass, string $field)
    {
        foreach ($reflectionClass->getMethods($this->ignoreProtected ? ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_PROTECTED|ReflectionMethod::IS_PRIVATE : ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()
                && $reflectionMethod->getNumberOfRequiredParameters() == 1
                && strcasecmp($reflectionMethod->getName(), "set$field") == 0) {
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
