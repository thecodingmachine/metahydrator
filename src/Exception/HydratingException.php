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
        $errorMessages = $this->getErrorMessage($errorsMap);
        $message .= "Detailed errors : \n " . implode(", ", $errorMessages);
        parent::__construct($message, $code, null);
        $this->errorsMap = $errorsMap;
    }

    private function getErrorMessage($errorsMap, $parentField = 'root')
    {
        $errorMessages = [];
        foreach ($errorsMap as $field => $error){
            $completeField = $parentField . '.' . $field;
            if (is_array($error)){
                $innerError = $this->getErrorMessage($error, $completeField);
                $errorMessages = array_merge($errorMessages, $innerError);
            }else if ($error !== null){
                $errorMessages[] = $completeField . " : " . $error;
            }
        }
        return $errorMessages;
    }
}
