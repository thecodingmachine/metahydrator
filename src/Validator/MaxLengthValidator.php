<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

/**
 * An implementation of ValidatorInterface used to limit size of a string
 */
class MaxLengthValidator extends AbstractValidator
{
    /** @var int */
    private $maxLength;
    public function getMaxLength() { return $this->maxLength; }
    public function setMaxLength(int $maxLength) { $this->maxLength = $maxLength; }

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     * @param string $errorMessage
     */
    public function __construct(int $maxLength, $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->maxLength = $maxLength;
    }

    /**
     * @param mixed $value
     * @param $contextObject
     *
     * @throws ValidationException
     */
    public function validate($value, $contextObject = null)
    {
        if ($value != null && strlen(strval($value)) > $this->maxLength) {
            $this->throw();
        }
    }
}
