<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Parser\BoolParser;

class BoolParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var BoolParser */
    private $parser;

    protected function setup()
    {
        $this->parser = new BoolParser();
    }

    public function testParse()
    {
        $this->assertSame(true, $this->parser->parse(true));
        $this->assertSame(true, $this->parser->parse('true'));
        $this->assertSame(true, $this->parser->parse(1));
        $this->assertSame(true, $this->parser->parse('1'));
        $this->assertSame(true, $this->parser->parse('yes'));
        $this->assertSame(true, $this->parser->parse('on'));
        $this->assertSame(false, $this->parser->parse(false));
        $this->assertSame(false, $this->parser->parse('false'));
        $this->assertSame(false, $this->parser->parse(0));
        $this->assertSame(false, $this->parser->parse('0'));
        $this->assertSame(false, $this->parser->parse('no'));
        $this->assertSame(false, $this->parser->parse('off'));
    }

    public function testParseNull()
    {
        $this->assertSame(null, $this->parser->parse(null));
        $this->assertSame(null, $this->parser->parse(''));
    }

    /**
     * @expectedException \MetaHydrator\Exception\ValidationException
     */
    public function testParseThrow()
    {
        $this->parser->parse('nope');
    }
}
