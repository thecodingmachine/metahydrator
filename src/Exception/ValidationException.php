<?php
namespace MetaHydrator\Exception;

/**
 * An exception describing why some data was considered invalid.
 *
 * Class ValidationException
 * @package MetaHydrator\Exception
 */
class ValidationException extends \Exception
{
    /** @var mixed */
    private $innerError;
    /** @return mixed */
    public function getInnerError() { return $this->innerError; }

    /**
     * ValidationException constructor.
     * @param mixed $innerError
     * @param string $message
     * @param int $code
     */
    public function __construct($innerError, $message = "", $code = 0)
    {
        parent::__construct($message, $code, null);
        $this->innerError = $innerError;
    }
}
