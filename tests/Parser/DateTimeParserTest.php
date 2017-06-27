<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Parser\DateTimeParser;

class DateTimeParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseFromFormat()
    {
        $parser = new DateTimeParser('d/m/Y');
        $parser->parse('01/04/2013');
    }

    public function testParseWithoutFormat()
    {
        $parser = new DateTimeParser();
        $parser->parse('yesterday');
    }

    public function testParseNull()
    {
        $parser = new DateTimeParser('d/m/Y');
        $parser->parse('');
        $parser->parse(null);
    }

    public function testParseIgnoreWarning()
    {
        $parser = new DateTimeParser('d/m/Y+', true);
        $parser->parse('01/04/2013 04:52:00');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testParseWarningThrow()
    {
        $parser = new DateTimeParser('d/m/Y+');
        $parser->parse('01/04/2013 04:52:00');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testParseFromFormatThrow()
    {
        $parser = new DateTimeParser('d/m/Y');
        $parser->parse('a/04/2013');
    }
}
