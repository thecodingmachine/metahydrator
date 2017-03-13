<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * A parser handling primitive input values.
 *
 * Interface ParserInterface
 * @package MetaHydrator\Parser
 */
interface ParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue);
}
