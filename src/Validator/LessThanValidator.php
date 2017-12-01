<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

class LessThanValidator extends AbstractValidator
{
    /** @var mixed */
    protected $max;

    public function __construct($max, $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->max = $max;
    }

    /**
     * @param mixed $value
     * @param $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null)
    {
        if ($value !== null && $value > $this->max) {
            $this->throw();
        }
    }
}
