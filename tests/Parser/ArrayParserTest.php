<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Parser\ArrayParser;
use MetaHydrator\Parser\IntParser;
use MetaHydrator\Validator\NotEmptyValidator;

class ArrayParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayParser */
    private $parser;

    function setUp()
    {
        $this->parser = new ArrayParser(new IntParser('type error'));
    }

    function testParse()
    {
        $parsed = $this->parser->parse(['3', '', '12', null]);
        $this->assertCount(4, $parsed);
        $this->assertSame(3, $parsed[0]);
        $this->assertSame(null, $parsed[1]);
        $this->assertSame(12, $parsed[2]);
        $this->assertSame(null, $parsed[3]);
    }

    function testParseNull()
    {
        $parsed = $this->parser->parse(null);
        $this->assertSame(null, $parsed);
        $parsed = $this->parser->parse([]);
        $this->assertSame([], $parsed);
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    function testParseThrow()
    {
        $this->parser->parse('');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    function testParseSubThrow()
    {
        try {
            $this->parser->addSubValidator(new NotEmptyValidator('required'));
            $this->parser->parse(['3', '', '12', null, ['foo']]);
        } catch (ParsingException $exception) {
            $errors = $exception->getInnerError();
            $this->assertCount(5, $errors);
            $this->assertSame(null, $errors[0]);
            $this->assertSame('required', $errors[1]);
            $this->assertSame(null, $errors[2]);
            $this->assertSame('required', $errors[3]);
            $this->assertSame('type error', $errors[4]);
            throw $exception;
        }
    }
}
