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
    /** @var string */
    private $errorMessage;
    /** @return string */
    public function getErrorMessage() { return $this->errorMessage; }

    /**
     * AbstractValidator constructor.
     * @param string $errorMessage
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
