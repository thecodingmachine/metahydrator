<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * Class StringParser
 * @package MetaHydrator\Parser
 */
class StringParser extends AbstractParser
{
    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }
        if (!is_scalar($rawValue)) {
            $this->throw();
        }
        return strval($rawValue);
    }
}
