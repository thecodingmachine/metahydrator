<?php

namespace MetaHydrator\Parser;


class SimpleArrayParser extends AbstractParser
{
    /**
     * @param $rawValue
     * @return mixed|null
     * @throws \MetaHydrator\Exception\ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        } else {
            $value = is_array($rawValue);
            if ($value === false) {
                $this->throw();
            }
            return $rawValue;
        }
    }
}
