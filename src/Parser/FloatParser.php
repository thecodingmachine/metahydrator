<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * An implementation of ParserInterface used to parse a float value.
 */
class FloatParser extends AbstractParser
{
    /**
     * @param $rawValue
     * @return float|null
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        }
        if(!is_float($rawValue) && !is_int($rawValue) && !is_numeric($rawValue)) {
            $this->throw();
        }
        return floatval($rawValue);
    }
}
