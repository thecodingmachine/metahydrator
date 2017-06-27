<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

/**
 * An implementation of ValidatorInterface used to limit allowed values to a list
 */
class EnumValidator extends AbstractValidator
{
    /** @var string[] */
    private $values;
    public function getValues() { return $this->values; }
    public function setValues(array $values) { $this->values = $values; }
    public function addValue(string $value) { $this->values[] = $value; }

    /**
     * EnumValidator constructor.
     * @param string[] $values
     * @param string $errorMessage
     */
    public function __construct(array $values, $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->values = $values;
    }

    /**
     * @param mixed $value
     * @param $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null)
    {
        if ($value !== null && $value !== '' && !in_array($value, $this->values)) {
            $this->throw();
        }
    }
}
