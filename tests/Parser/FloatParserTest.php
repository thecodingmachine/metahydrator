<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ValidationException;
use MetaHydrator\Parser\FloatParser;

class FloatParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var FloatParser */
    private $parser;

    protected function setup()
    {
        $this->parser = new FloatParser();
    }

    public function testParse()
    {
        $this->assertSame(1.314, $this->parser->parse("1.314"));
        $this->assertSame(4.0, $this->parser->parse("4.0"));
        $this->assertSame(4.0, $this->parser->parse("4"));
        $this->assertSame(1.314, $this->parser->parse(1.314));
        $this->assertSame(4.0, $this->parser->parse(4.0));
        $this->assertSame(4.0, $this->parser->parse(4));
        $this->assertSame(1000.0, $this->parser->parse("1e3"));
    }

    public function testParseNull()
    {
        $this->assertSame(null, $this->parser->parse(""));
        $this->assertSame(null, $this->parser->parse(null));
    }

    public function testParseThrow()
    {
        foreach(['1 356', '4,5', '10 little piggies', 'teapot'] as $val) {
            try {
                $this->parser->parse($val);
                $this->assertFalse(true, 'Expected ValidationException');
            } catch (ValidationException $exception) {}
        }
    }
}
