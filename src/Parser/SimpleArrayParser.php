<?php

namespace MetaHydrator\Parser;

class SimpleArrayParser extends AbstractParser
{
    /**
     * @param array[] $rawValue
     * @return array[]|null
     * @throws \MetaHydrator\Exception\ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }
        if (is_array($rawValue) === false) {
            $this->throw();
        }
        return $rawValue;
    }
}
