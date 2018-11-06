<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 06/11/18
 * Time: 15:14
 */

namespace MetaHydratorTest\Parser;


use MetaHydrator\Parser\SimpleArrayParser;

class SimpleArrayParserTest
{
    public function testParseSimpleArray()
    {
        $parser = new SimpleArrayParser();

        try {
            $parser->parse('');
            $parser->parse(["random", "lambda", 42]);
            $parser->parse(null);
        }catch (\Exception $e){
            echo $e;
        }
    }
}
