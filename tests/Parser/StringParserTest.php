<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Parser\StringParser;

class StringParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseString()
    {
        $parser = new StringParser();

        $parser->parse('foo');
        $parser->parse(42);
        $parser->parse(4.2);
    }
}
