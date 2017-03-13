<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Parser\IntParser;

class IntParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseInt()
    {
        $parser = new IntParser();

        $val = $parser->parse(3);
        $this->assertTrue($val === 3);

        $val = $parser->parse('3');
        $this->assertTrue($val === 3);

        $val = $parser->parse('');
        $this->assertTrue($val === null);

        $val = $parser->parse(null);
        $this->assertTrue($val === null);

        try {
            $parser->parse('1.00');
            self::assertTrue(false);
        } catch (ParsingException $exception) {
            self::assertTrue(true);
        }

        try {
            $parser->parse(1.35);
            self::assertTrue(false);
        } catch (ParsingException $exception) {
            self::assertTrue(true);
        }

        try {
            $parser->parse('I\'m no integer');
            self::assertTrue(false);
        } catch (ParsingException $exception) {
            self::assertTrue(true);
        }
    }

}
