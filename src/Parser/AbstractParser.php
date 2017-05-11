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
     * @throws ParsingException
     */
    protected function throw()
    {
        throw new ParsingException($this->errorMessage);
    }
}
