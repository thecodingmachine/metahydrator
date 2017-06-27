<?php
namespace MetaHydrator\Reflection;

/**
 * interface to retrieve a value from an object, given a field name
 *
 * Interface GetterInterface
 * @package MetaHydrator\Reflection
 */
interface GetterInterface
{
    /**
     * @param $object
     * @param string $field
     * @return mixed
     */
    public function get($object, string $field);
}
