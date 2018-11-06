<?php
namespace MetaHydratorTest;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Handler\SimpleHydratingHandler;
use MetaHydrator\Handler\SubArrayHydratingHandler;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Parser\IntParser;
use MetaHydrator\Parser\StringParser;
use MetaHydrator\Validator\NotEmptyValidator;

class SubArrayHydratingHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var MetaHydrator */
    private $hydrator;

    public function setUp()
    {
        $this->hydrator = new MetaHydrator([
            new SimpleHydratingHandler('foo', new StringParser('Invalid string')),
            new SubArrayHydratingHandler('garply', FooBar::class,
                new MetaHydrator([
                    new SimpleHydratingHandler('foo', new StringParser('Invalid string')),
                    new SimpleHydratingHandler('bar', new IntParser('Invalid integer'))
                ]), [], 'Invalid array', 'Invalid FooBar'
            )
        ]);
    }

    public function testHydrateNew()
    {
        $data = [
            'foo' => 'Joe',
            'garply' => [
                [ 'foo' => 'Jack' ],
                null,
                [ 'foo' => 'Averell' ],
            ]
        ];

        /** @var FooBar $fooBar */
        $fooBar = $this->hydrator->hydrateNewObject($data, FooBar::class);

        $this->assertEquals('Joe', $fooBar->getFoo());

        $garplies = $fooBar->getGarply();
        $this->assertCount(2, $garplies);

        $this->assertEquals('Jack', $garplies[0]->getFoo());
        $this->assertEquals('Averell', $garplies[1]->getFoo());
    }

    public function testHydrateNewEmpty()
    {
        $data = [
            'foo' => 'Joe',
            'garply' => []
        ];

        /** @var FooBar $fooBar */
        $fooBar = $this->hydrator->hydrateNewObject($data, FooBar::class);
        $garplies = $fooBar->getGarply();
        $this->assertSame([], $garplies);
    }

    public function testHydrateNewNull()
    {
        $data = [
            'foo' => 'Joe',
            'garply' => null
        ];

        /** @var FooBar $fooBar */
        $fooBar = $this->hydrator->hydrateNewObject($data, FooBar::class);
        $garplies = $fooBar->getGarply();
        $this->assertSame(null, $garplies);
    }

    public function testHydrateNewUndefined()
    {
        $data = [
            'foo' => 'Joe'
        ];

        /** @var FooBar $fooBar */
        $fooBar = $this->hydrator->hydrateNewObject($data, FooBar::class);
        $garplies = $fooBar->getGarply();
        $this->assertSame(null, $garplies);
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateNewInvalid()
    {
        $data = [
            'foo' => 'Joe',
            'garply' => 'array(0)[]'
        ];

        $this->hydrator->hydrateNewObject($data, FooBar::class);
    }

    public function testHydrateObject()
    {
        $data = [
            'garply' => [
                1 => null,
                2 => [
                    'foo' => 'Ma'
                ]
            ]
        ];

        $fooBar = $this->getFooBar();
        $this->hydrator->hydrateObject($data, $fooBar);

        $garplies = $fooBar->getGarply();
        $this->assertCount(2, $garplies);
        $this->assertEquals('Jack', $garplies[0]->getFoo());
        $this->assertEquals('Ma', $garplies[1]->getFoo());
    }

    public function testHydrateObjectEmpty()
    {
        $fooBar = $this->getFooBar();

        $data = [
            'garply' => []
        ];

        $this->hydrator->hydrateObject($data, $fooBar);

        $garplies = $fooBar->getGarply();
        $this->assertCount(3, $garplies);
        $this->assertEquals('Jack', $garplies[0]->getFoo());
        $this->assertEquals('William', $garplies[1]->getFoo());
        $this->assertEquals('Averell', $garplies[2]->getFoo());
    }

    public function testHydrateObjectNull()
    {
        $fooBar = $this->getFooBar();

        $data = [
            'garply' => null
        ];

        $this->hydrator->hydrateObject($data, $fooBar);

        $garplies = $fooBar->getGarply();
        $this->assertSame(null, $garplies);
    }

    public function testHydrateObjectUndefined()
    {
        $fooBar = $this->getFooBar();

        $data = [
            'foo' => 'Lucky Luke',
        ];

        $this->hydrator->hydrateObject($data, $fooBar);

        $garplies = $fooBar->getGarply();
        $this->assertCount(3, $garplies);
        $this->assertEquals('Jack', $garplies[0]->getFoo());
        $this->assertEquals('William', $garplies[1]->getFoo());
        $this->assertEquals('Averell', $garplies[2]->getFoo());
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateObjectInvalid()
    {
        $fooBar = $this->getFooBar();

        $data = [
            'foo' => 'Lucky Luke',
            'garply' => 'array(0)[]'
        ];

        try {
            $this->hydrator->hydrateObject($data, $fooBar);
        } catch (HydratingException $exception) {
            $errors = $exception->getErrorsMap();
            $this->assertArrayHasKey('garply', $errors);
            $this->assertSame('Invalid array', $errors['garply']);
            throw $exception;
        }
    }

    public function testHydrateNewAssoc()
    {
        $this->hydrator->getHandlers()[1]->setAssociative(true);
        $data = [
            'foo' => 'Joe',
            'garply' => [
                [ 'foo' => 'Jack' ],
                null,
                [ 'foo' => 'Averell' ],
            ]
        ];

        /** @var FooBar $fooBar */
        $fooBar = $this->hydrator->hydrateNewObject($data, FooBar::class);

        $this->assertEquals('Joe', $fooBar->getFoo());

        $garplies = $fooBar->getGarply();
        $this->assertCount(2, $garplies);

        $this->assertEquals('Jack', $garplies[0]->getFoo());
        $this->assertArrayNotHasKey(1, $garplies);
        $this->assertEquals('Averell', $garplies[2]->getFoo());
    }

    public function testHydrateObjectAssoc()
    {
        $this->hydrator->getHandlers()[1]->setAssociative(true);
        $fooBar = $this->getFooBar();

        $data = [
            'garply' => [
                0 => [ 'foo' => 'Ma' ],
                1 => null,
            ]
        ];

        $this->hydrator->hydrateObject($data, $fooBar);

        $garplies = $fooBar->getGarply();
        $this->assertCount(2, $garplies);

        $this->assertEquals('Ma', $garplies[0]->getFoo());
        $this->assertArrayNotHasKey(1, $garplies);
        $this->assertEquals('Averell', $garplies[2]->getFoo());
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateNewSubInvalid()
    {
        $data = [
            'garply' => [
                0 => [ 'bar' => 'Two' ],
            ]
        ];

        try {
            $this->hydrator->hydrateNewObject($data, FooBar::class);
        } catch (HydratingException $exception) {
            $errors = $exception->getErrorsMap();
            $this->assertArrayHasKey('garply', $errors);
            $this->assertArrayHasKey(0, $errors['garply']);
            $this->assertArrayHasKey('bar', $errors['garply'][0]);
            $this->assertSame('Invalid integer', $errors['garply'][0]['bar']);
            throw $exception;
        }
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateObjectSubInvalid()
    {
        $data = [
            'garply' => [
                0 => [ 'bar' => 'Two' ],
            ]
        ];

        try {
            $this->hydrator->hydrateObject($data, $this->getFooBar());
        } catch (HydratingException $exception) {
            $errors = $exception->getErrorsMap();
            $this->assertArrayHasKey('garply', $errors);
            $this->assertArrayHasKey(0, $errors['garply']);
            $this->assertArrayHasKey('bar', $errors['garply'][0]);
            $this->assertSame('Invalid integer', $errors['garply'][0]['bar']);
            throw $exception;
        }
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateNewInvalidArray()
    {
        $data = [
            'garply' => [
                0 => 'FooBar("Ma", 42)',
            ]
        ];

        try {
            $this->hydrator->hydrateObject($data, $this->getFooBar());
        } catch (HydratingException $exception) {
            $errors = $exception->getErrorsMap();
            $this->assertArrayHasKey('garply', $errors);
            $this->assertArrayHasKey(0, $errors['garply']);
            $this->assertSame('Invalid FooBar', $errors['garply'][0]);
            throw $exception;
        }
    }

    /**
     * @expectedException \MetaHydrator\Exception\HydratingException
     */
    public function testHydrateObjectValidateThrow()
    {
        $this->hydrator->getHandlers()[1]->addValidator(new NotEmptyValidator('FooBars required'));
        $data = [
            'garply' => []
        ];

        try {
            $this->hydrator->hydrateNewObject($data, FooBar::class);
        } catch (HydratingException $exception) {
            $errors = $exception->getErrorsMap();
            $this->assertArrayHasKey('garply', $errors);
            $this->assertSame('FooBars required', $errors['garply']);
            throw $exception;
        }
    }

    private function getFooBar()
    {
        return new FooBar([
            'foo' => 'Joe',
            'garply' => [
                new FooBar([ 'foo' => 'Jack' ]),
                new FooBar([ 'foo' => 'William' ]),
                new FooBar([ 'foo' => 'Averell' ]),
            ]
        ]);
    }
}
