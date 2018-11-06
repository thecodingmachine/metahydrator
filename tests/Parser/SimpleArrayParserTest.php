<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Parser\SimpleArrayParser;

class SimpleArrayParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseSimpleArray()
    {
        $parser = new SimpleArrayParser();

        $this->assertSame(["random", "lambda", 42, null], $parser->parse(["random", "lambda", 42, null]));
        $this->assertSame(null, $parser->parse(null));

        $this->expectException(ParsingException::class);
        $parser->parse('');
    }
}
