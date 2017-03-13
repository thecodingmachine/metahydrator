<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

/**
 * A validator to refuse null or empty value
 *
 * Class NotEmptyValidator
 * @package MetaHydrator\Validator
 */
class NotEmptyValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @param mixed $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null)
    {
        if ($value === '' || $value === null || $value === []) {
            $this->throw();
        }
    }
}
