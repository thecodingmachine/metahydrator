<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

class GreaterThanValidator extends AbstractValidator
{
    /** @var mixed */
    protected $min;

    public function __construct($min, $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->min = $min;
    }

    /**
     * @param mixed $value
     * @param $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null)
    {
        if ($value !== null && $value < $this->min) {
            $this->throw();
        }
    }
}
