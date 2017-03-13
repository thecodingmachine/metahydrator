<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * Class IntParser
 * @package MetaHydrator\Parser
 */
class IntParser extends AbstractParser
{
    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        } else if (!is_int($rawValue) && !ctype_digit($rawValue)) {
            $this->throw();
        } else {
            return intval($rawValue);
        }
    }
}
