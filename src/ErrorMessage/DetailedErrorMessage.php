<?php
namespace MetaHydrator\ErrorMessage;

/**
 * A descriptor for a specific failure in form data parsing/validation.
 *
 * Class DetailedErrorMessage
 * @package MetaHydrator\ErrorMessage
 */
class DetailedErrorMessage extends SimpleErrorMessage
{
    /** @var string */
    private $details;
    /** @return string */
    public function getDetails() { return $this->details; }

    /**
     * FieldError constructor.
     * @Important
     * @param string $error
     * @param string $details
     */
    public function __construct($message = '', $details = '')
    {
        parent::__construct($message);
        $this->details = $details;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'message' => $this->getMessage(),
            'details' => $this->details
        ];
    }
}
