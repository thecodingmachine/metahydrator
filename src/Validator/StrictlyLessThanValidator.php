<?php
namespace MetaHydrator\Validator;

class StrictlyLessThanValidator extends LessThanValidator
{
    public function validate($value, $contextObject = null)
    {
        if ($value !== null && $value >= $this->max) {
            $this->throw();
        }
    }
}
