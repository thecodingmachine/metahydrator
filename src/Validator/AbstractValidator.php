<?php
namespace MetaHydrator\Validator;

use MetaHydrator\Exception\ValidationException;

/**
 * An abstract validator with configurable error message.
 *
 * Class AbstractValidator
 * @package MetaHydrator\Validator
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /** @var mixed */
    private $errorMessage;
    /** @return mixed */
    public function getErrorMessage() { return $this->errorMessage; }

    /**
     * AbstractValidator constructor.
     * @param mixed $errorMessage
     */
    public function __construct($errorMessage = "")
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @throws ValidationException
     */
    protected function throw()
    {
        throw new ValidationException($this->errorMessage);
    }
}
