<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Handler\SimpleHydratingHandler;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Parser\ObjectParser;
use MetaHydrator\Parser\StringParser;
use MetaHydrator\Validator\NotEmptyValidator;
use MetaHydratorTest\FooBar;

class ObjectParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectParser */
    private $parser;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->parser = new ObjectParser(FooBar::class, new MetaHydrator([
            new SimpleHydratingHandler('foo', new StringParser(), [new NotEmptyValidator()]),
        ], []), 'This cannot be a FooBar');
    }

    public function testParseValidObject()
    {
        try {
            $parsed = $this->parser->parse([
                'foo' => 'malesuada'
            ]);
            self::assertTrue($parsed instanceof FooBar);
            self::assertTrue($parsed->getFoo() == 'malesuada');
        } catch (ParsingException $exception) {
            self::assertFalse(true);
        }
    }

    public function testParseInvalidObject()
    {
        try {
            $parsed = $this->parser->parse([
                'foo' => null,
            ]);
            self::assertFalse(true);
        } catch (ParsingException $exception) {
            self::assertArrayHasKey('foo', $exception->getInnerError());

            return;
        }
        self::assertFalse(true);
    }

    public function testParseInvalidType()
    {
        try {
            $parsed = $this->parser->parse('ALL WRONG');
            self::assertFalse(true);
        } catch (ParsingException $exception) {
            self::assertTrue($exception->getInnerError() === 'This cannot be a FooBar');

            return;
        }
        self::assertFalse(true);
    }
}
