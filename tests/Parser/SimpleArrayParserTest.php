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
        $this->assertSame(["random", "lambda", 42], $parser->parse(["random", "lambda", 42]));
        $this->assertSame(["random", "lambda"], $parser->parse(["random", "lambda"]));
        $this->assertSame([21, 42], $parser->parse([21, 42]));
        $this->assertSame([42], $parser->parse([42]));
        $this->assertSame([], $parser->parse([]));


        $this->expectException(ParsingException::class);
        $parser->parse('');

        $this->expectException(ParsingException::class);
        $parser->parse(null);
    }
}
