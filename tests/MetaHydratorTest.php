<?php
namespace MetaHydratorTest;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Handler\SimpleHydratingHandler;
use MetaHydrator\Handler\SubHydratingHandler;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Parser\ArrayParser;
use MetaHydrator\Parser\IntParser;
use MetaHydrator\Parser\ObjectParser;
use MetaHydrator\Parser\StringParser;
use MetaHydrator\Validator\NotEmptyValidator;

class MetaHydratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MetaHydrator */
    private $hydrator;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->hydrator = new MetaHydrator([
                new SimpleHydratingHandler('foo', new StringParser()),
                new SimpleHydratingHandler('bar', new IntParser(), [new NotEmptyValidator('This field is required')]),
                new SimpleHydratingHandler('baz', new IntParser()),
                new SubHydratingHandler('qux', FooBar::class, new MetaHydrator([
                    new SimpleHydratingHandler('foo', new StringParser()),
                    new SimpleHydratingHandler('corge', new StringParser()),
                ])),
                new SubHydratingHandler('quux', FooBar::class, new MetaHydrator([
                    new SimpleHydratingHandler('foo', new StringParser()),
                    new SimpleHydratingHandler('corge', new StringParser()),
                ])),
                new SimpleHydratingHandler('grault', new ArrayParser(
                    new IntParser()
                )),
                new SimpleHydratingHandler('waldo',
                    new ObjectParser(FooBar::class, new MetaHydrator([
                        new SimpleHydratingHandler('foo', new StringParser(), [new NotEmptyValidator('This field is required')]),
                    ]))
                ),
                new SimpleHydratingHandler('garply', new ArrayParser(
                    new ObjectParser(FooBar::class, new MetaHydrator([
                        new SimpleHydratingHandler('foo', new StringParser())
                    ]))
                )),
                new SubHydratingHandler('blah', FooBar::class, new MetaHydrator([
                    new SimpleHydratingHandler('garply', new ArrayParser(
                        new ObjectParser(FooBar::class, new MetaHydrator([
                            new SimpleHydratingHandler('foo', new StringParser(), [new NotEmptyValidator('This field is required')])
                        ]))
                    )),
                ])),
            ]
        );
    }

    public function testCreateFromValidForm()
    {
        try {
            /** @var FooBar $fooBar */
            $fooBar = $this->hydrator->hydrateNewObject([
                'foo' => 'str',
                'bar' => 13,
                'baz' => ''
            ], FooBar::class);
            $this->assertTrue($fooBar->getFoo() === 'str');
            $this->assertTrue($fooBar->getBar() === 13);
            $this->assertTrue($fooBar->getBaz() === null);
        } catch (HydratingException $exception) {
            self::assertTrue(false, 'form data was supposed to be valid!');
        }
    }

    public function testApplyValidForm()
    {
        try {
            $fooBar = new FooBar();
            $fooBar->setFoo('bla');
            $fooBar->setBar(0);
            $fooBar->setBaz(1);
            $fooBar->setQux(new FooBar([
                'foo' => 'lorem',
                'bar' => 13
            ]));
            $fooBar->setWaldo(new FooBar([
                'foo' => 'speck',
                'bar' => 20
            ]));

            $this->hydrator->hydrateObject([
                'foo' => null,
                'baz' => '42',
                'qux' => [
                    'foo' => 'deserunt',
                    'corge' => 'ipsum',
                ],
                'quux' => [
                    'foo' => 'dolor',
                    'corge' => 'consectetur',
                ],
                'grault' => [
                    13,
                    14,
                    15,
                ],
                'waldo' => [
                    'foo' => 'assertum'
                ],
                'garply' => [
                    [ 'foo' => 'turpis' ],
                    [ 'foo' => 'condimentum' ],
                    [ 'foo' => 'pretium' ],
                ]
            ], $fooBar);

            $this->assertTrue($fooBar->getFoo() === null);
            $this->assertTrue($fooBar->getBar() === 0);
            $this->assertTrue($fooBar->getBaz() === 42);

            $this->assertTrue($fooBar->getQux() instanceof FooBar);
            $this->assertTrue($fooBar->getQux()->getFoo() == 'deserunt');
            $this->assertTrue($fooBar->getQux()->getBar() == 13);
            $this->assertTrue($fooBar->getQux()->getCorge() == 'ipsum');

            $this->assertTrue($fooBar->getQuux() instanceof FooBar);
            $this->assertTrue($fooBar->getQuux()->getFoo() == 'dolor');
            $this->assertTrue($fooBar->getQuux()->getCorge() == 'consectetur');

            $this->assertTrue($fooBar->getWaldo() !== null);
            $this->assertTrue($fooBar->getWaldo() instanceof FooBar);
            $this->assertTrue($fooBar->getWaldo()->getFoo() == 'assertum');
            $this->assertTrue($fooBar->getWaldo()->getBar() === null);

            $this->assertTrue($fooBar->getGrault() == [13, 14, 15]);

            $this->assertTrue(is_array($fooBar->getGarply()));
            $this->assertTrue($fooBar->getGarply()[0]->getFoo() == 'turpis');
            $this->assertTrue($fooBar->getGarply()[1]->getFoo() == 'condimentum');
            $this->assertTrue($fooBar->getGarply()[2]->getFoo() == 'pretium');
        } catch (HydratingException $exception) {
            self::assertTrue(false, 'form data was supposed to be valid!');
        }
    }

    public function testParseInvalidForm()
    {
        try {
            $this->hydrator->hydrateNewObject([
                'foo' => ['blah'],
                'baz' => '1.35',
                'qux' => 'wrong type',
            ], FooBar::class);
            $this->assertTrue(false, 'form data was supposed to be INvalid!');
        } catch (HydratingException $exception) {
            self::assertTrue(true);
            $errorsMap = $exception->getErrorsMap();
            self::assertArrayHasKey('foo', $errorsMap);
            self::assertArrayHasKey('bar', $errorsMap);
        }
    }

    public function testCreateFromInvalidForm()
    {
        $fooBar = new FooBar();
        try {
            $this->hydrator->hydrateObject([
                'foo' => ['blah'],
                'baz' => '1.35',
                'qux' => 'wrong type',
                'blah' => [
                    'garply' => [
                        [ ],
                        [ 'foo' => 'sollicitudin' ],
                        [ 'foo' => null ],
                    ],
                ]
            ], $fooBar);
            $this->assertTrue(false, 'form data was supposed to be INvalid!');
        } catch (HydratingException $exception) {
            self::assertTrue(true);
            $errorsMap = $exception->getErrorsMap();
            self::assertArrayHasKey('foo', $errorsMap);
            self::assertArrayHasKey('blah', $errorsMap);
            self::assertArrayHasKey('garply', $errorsMap['blah']);
            self::assertArrayHasKey(0, $errorsMap['blah']['garply']);
            self::assertNull($errorsMap['blah']['garply'][1]);
            self::assertArrayHasKey(2, $errorsMap['blah']['garply']);
        }
    }
}
