<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/*
 * An implementation of ParserInterface used to parse a boolean value.
 * Values allowed are true, "true", 1, "1", "yes", "on", false, "false", 0, "0", "no", "off"
 */
class BoolParser extends AbstractParser
{
    /**
     * @param $rawValue
     * @return bool|null
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        } else {
            $value = filter_var($rawValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value === null) {
                $this->throw();
            }
            return $value;
        }
    }
}
