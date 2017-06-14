<?php
namespace MetaHydratorTest\Reflection;

use MetaHydrator\Reflection\Setter;

class SetterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPublicSetter()
    {
        $setter = new Setter();
        $object = new class {
            public $__;
            public function setValue($value) { $this->__ = $value; }
        };
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object->__);
    }

    public function testSetPrivateSetter()
    {
        $setter = new Setter(true, true);
        $object = new class {
            public $__;
            private function setValue($value) { $this->__ = $value; }
        };
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object->__);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testSetPrivateSetterThrow()
    {
        $setter = new Setter();
        $object = new class {
            public $__;
            private function setValue($value) { $this->__ = $value; }
        };
        $setter->set($object, 'value', 3);
    }

    public function testSetPrivateSetterIgnore()
    {
        $setter = new Setter(false);
        $object = new class {
            public $__;
            private function setValue($value) { $this->__ = $value; }
        };
        $setter->set($object, 'value', 3);
        $this->assertNotEquals(3, $object->__);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testSetStaticSetterThrow()
    {
        $setter = new Setter();
        $object = new class {
            static function setValue($value) { throw new \Exception('Who is the one who knocks?'); }
        };
        $setter->set($object, 'value', 3);
    }

    public function testSetPublicProperty()
    {
        $setter = new Setter();
        $object = new class {
            public $value = 3;
        };
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object->value);
    }

    public function testSetPrivateProperty()
    {
        $setter = new Setter(true, true);
        $object = new class {
            private $value = 3;
            public function __() { return $this->value; }
        };
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object->__());
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testSetPrivatePropertyThrow()
    {
        $setter = new Setter();
        $object = new class {
            private $value = 3;
        };
        $setter->set($object, 'value', 3);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testSetStaticPropertyThrow()
    {
        $setter = new Setter();
        $object = new class {
            static $value = 3;
        };
        $setter->set($object, 'value', 3);
    }

    public function testSetInArray()
    {
        $setter = new Setter();
        $object = [
            'value' => 3
        ];
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object['value']);
    }

    public function testSetInArrayAccess()
    {
        $setter = new Setter();
        $object = new class implements \ArrayAccess {
            public $__ = [];
            public function offsetExists($offset) { return array_key_exists($offset, $this->__); }
            public function offsetGet($offset) { return $this->__[$offset]; }
            public function offsetSet($offset, $value) { $this->__[$offset] = $value; }
            public function offsetUnset($offset) { unset($this->__[$offset]); }
        };
        $setter->set($object, 'value', 3);
        $this->assertEquals(3, $object['value']);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testSetFromInvalidThrow()
    {
        $setter = new Setter();
        $object = 2;
        $setter->set($object, 'value', 3);
    }

    public function testSetFromInvalidIgnore()
    {
        $setter = new Setter(false);
        $object = 2;
        $setter->set($object, 'value', 3);
        $this->assertEquals(2, $object);
    }
}
