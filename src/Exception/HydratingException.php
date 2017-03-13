<?php
namespace MetaHydrator\Exception;

/**
 * An exception containing the mapping of various errors in a form data.
 *
 * Class HydratingException
 * @package MetaHydrator\Exception
 */
class HydratingException extends \Exception
{
    /** @var array */
    private $errorsMap;
    /** @return array */
    public function getErrorsMap() { return $this->errorsMap; }

    /**
     * HydratingException constructor.
     * All the leaves of errors map should describe a specific error.
     *
     * @param array $errorsMap
     * @param string $message
     * @param int $code
     */
    public function __construct($errorsMap, $message = "", $code = 412)
    {
        parent::__construct($message, $code, null);
        $this->errorsMap = $errorsMap;
    }
}
