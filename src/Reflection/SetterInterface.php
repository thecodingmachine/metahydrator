<?php
namespace MetaHydrator\Reflection;

/**
 * Interface SetterInterface
 * @package MetaHydrator\Reflection
 */
interface SetterInterface
{
    /**
     * @param $object
     * @param string $field
     * @param mixed $value
     */
    public function set(&$object, string $field, $value);
}
