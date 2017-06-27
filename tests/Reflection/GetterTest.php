<?php
namespace MetaHydratorTest\Reflection;

use MetaHydrator\Reflection\Getter;

class GetterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPublicGetter()
    {
        $getter = new Getter();
        $object = new class {
            public function getValue() { return 3; }
        };
        $value = $getter->get($object, 'value');
        $this->assertEquals(3, $value);
    }

    public function testGetPrivateGetter()
    {
        $getter = new Getter(true, true);
        $object = new class {
            private function getValue() { return 3; }
        };
        $value = $getter->get($object, 'value');
        $this->assertEquals(3, $value);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetPrivateGetterThrow()
    {
        $getter = new Getter();
        $object = new class {
            private function getValue() { return 3; }
        };
        $getter->get($object, 'value');
    }

    public function testGetPrivateGetterIgnore()
    {
        $getter = new Getter(false);
        $object = new class {
            private function getValue() { return 3; }
        };
        $value = $getter->get($object, 'value');
        $this->assertEquals(null, $value);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetStaticGetterThrow()
    {
        $getter = new Getter();
        $object = new class {
            static function getValue() { return 3; }
        };
        $getter->get($object, 'value');
    }

    public function testGetPublicProperty()
    {
        $getter = new Getter();
        $object = new class {
            public $value = 3;
        };
        $value = $getter->get($object, 'value');
        $this->assertEquals(3, $value);
    }

    public function testGetPrivateProperty()
    {
        $getter = new Getter(true, true);
        $object = new class {
            private $value = 3;
        };
        $value = $getter->get($object, 'value');
        $this->assertEquals(3, $value);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetPrivatePropertyThrow()
    {
        $getter = new Getter();
        $object = new class {
            private $value = 3;
        };
        $getter->get($object, 'value');
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetStaticPropertyThrow()
    {
        $getter = new Getter();
        $object = new class {
            static $value = 3;
        };
        $getter->get($object, 'value');
    }

    public function testGetFromArray()
    {
        $getter = new Getter();
        $object = [
            'value' => 3
        ];
        $value = $getter->get($object, 'value');
        $this->assertEquals(3, $value);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetFromArrayThrow()
    {
        $getter = new Getter();
        $object = [];
        $getter->get($object, 'value');
    }

    public function testGetFromArrayIgnore()
    {
        $getter = new Getter(false);
        $object = [];
        $value = $getter->get($object, 'value');
        $this->assertEquals(null, $value);
    }

    public function testGetFromArrayAccess()
    {
        $getter = new Getter();
        $object = new class implements \ArrayAccess {
            public function offsetExists($offset) { return $offset === "value"; }
            public function offsetGet($offset) { return 3; }
            public function offsetSet($offset, $value) { }
            public function offsetUnset($offset) { }
        };
        $getter->get($object, 'value');
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetFromArrayAccessThrow()
    {
        $getter = new Getter();
        $object = new class implements \ArrayAccess {
            public function offsetExists($offset) { return false; }
            public function offsetGet($offset) { return 3; }
            public function offsetSet($offset, $value) { }
            public function offsetUnset($offset) { }
        };
        $getter->get($object, 'value');
    }

    public function testGetFromArrayAccessIgnore()
    {
        $getter = new Getter(false);
        $object = new class implements \ArrayAccess {
            public function offsetExists($offset) { return false; }
            public function offsetGet($offset) { return 3; }
            public function offsetSet($offset, $value) { }
            public function offsetUnset($offset) { }
        };
        $getter->get($object, 'value');
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetFromInvalidThrow()
    {
        $getter = new Getter();
        $object = 3;
        $getter->get($object, 'value');
    }

    public function testGetFromInvalidIgnore()
    {
        $getter = new Getter(false);
        $object = 3;
        $value = $getter->get($object, 'value');
        $this->assertEquals(null, $value);
    }
}
