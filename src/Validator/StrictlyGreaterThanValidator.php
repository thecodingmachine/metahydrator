<?php
namespace MetaHydrator\Validator;

class StrictlyGreaterThanValidator extends GreaterThanValidator
{
    public function validate($value, $contextObject = null)
    {
        if ($value !== null && $value <= $this->min) {
            $this->throw();
        }
    }
}
