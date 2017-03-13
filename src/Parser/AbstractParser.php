<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * An abstract validator with configurable error message.
 *
 * Class AbstractParser
 * @package MetaHydrator\Parser
 */
abstract class AbstractParser implements ParserInterface
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
     * @throws ParsingException
     */
    protected function throw()
    {
        throw new ParsingException($this->errorMessage);
    }
}
