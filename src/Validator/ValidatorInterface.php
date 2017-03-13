<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

/**
 * A validator should check the sanity of a value, given a context object, and throw an exception in case of failure.
 *
 * Interface ValidatorInterface
 * @package MetaHydrator\Validator
 */
interface ValidatorInterface
{
    /**
     * @param mixed $value
     * @param $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null);
}
