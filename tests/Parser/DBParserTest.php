<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Handler\SimpleHydratingHandler;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Parser\DBParser;
use MetaHydrator\Parser\StringParser;
use MetaHydrator\Validator\MaxLengthValidator;
use MetaHydratorTest\Database\StubDBProvider;
use MetaHydratorTest\FooBar;

class DBParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var DBParser */
    private $dbParser;

    public function setUp()
    {
        $provider = new StubDBProvider([
            'foobar' => [
                'class' => FooBar::class,
                'pk' => ['foo'],
                'items' => [
                    new FooBar([
                        'foo' => '2',
                        'bar' => 'pilgrim'
                    ]),
                    new FooBar([
                        'foo' => 43,
                        'bar' => 'scott'
                    ])
                ]
            ]
        ]);
        $this->dbParser = new DBParser($provider, 'foobar', new MetaHydrator([
            new SimpleHydratingHandler('bar', new StringParser(), [ new MaxLengthValidator(25) ])
        ]));
    }

    public function testParseNull()
    {
        $object = $this->dbParser->parse(null);
        $this->assertNull($object);
    }

    public function testParseNew()
    {
        /** @var FooBar $object */
        $object = $this->dbParser->parse(['bar' => 'scotty']);
        $this->assertEquals('scotty', $object->getBar());
    }

    public function testParseEdit()
    {
        /** @var FooBar $object */
        $object = $this->dbParser->parse(['foo' => 2, 'bar' => 'scotty']);
        $this->assertEquals(FooBar::class, get_class($object));
        $this->assertEquals('scotty', $object->getBar());
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testFailInvalid()
    {
        $this->dbParser->parse('bla bla bla');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testFailHydrate()
    {
        try {
            $this->dbParser->parse(['bar' => "Wow, warm sailor. you won't drink the lighthouse."]);
        } catch (ParsingException $exception) {
            $errors = $exception->getInnerError();
            $this->assertArrayHasKey('bar', $errors);
            throw $exception;
        }
    }
}
