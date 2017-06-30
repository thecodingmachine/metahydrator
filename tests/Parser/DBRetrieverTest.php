<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Parser\DBRetriever;
use MetaHydratorTest\Database\StubDBProvider;
use MetaHydratorTest\FooBar;

class DBRetrieverTest extends \PHPUnit_Framework_TestCase
{
    /** @var DBRetriever */
    private $dbRetriever;

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
        $this->dbRetriever = new DBRetriever($provider, 'foobar');
    }

    public function testRetrieveNull()
    {
        $object = $this->dbRetriever->parse(null);
        $this->assertNull($object);
    }

    public function testRetrieve()
    {
        /** @var FooBar $object */
        $object = $this->dbRetriever->parse(['foo' => 2, 'bar' => 'scott']);
        $this->assertEquals('pilgrim', $object->getBar());
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testFailNotFound()
    {
        $this->dbRetriever->parse(['foo' => 45]);
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testFailInvalid()
    {
        $this->dbRetriever->parse('pilgrim');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testFailWrongTable()
    {
        $this->dbRetriever->setTable('foobaz');
        $this->dbRetriever->parse(['foo' => 2]);
    }

    /**
     * @expectedException \MetaHydrator\Exception\ParsingException
     */
    public function testRetrieveFailMissingParams()
    {
        $this->dbRetriever->getDbProvider()->db['foobar']['pk'] = ['foo', 'bar'];
        $this->dbRetriever->parse(['foo' => 2]);
    }
}
