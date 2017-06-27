<?php
namespace MetaHydratorTest;

use MetaHydrator\Reflection\Setter;

class FooBar implements \JsonSerializable
{
    public function __construct($values = [])
    {
        $setter = new Setter(false);
        foreach ($values as $key => $value) {
            $setter->set($this, $key, $value);
        }
    }

    /** @var string */
    private $foo;
    /** @return string */
    public function getFoo() { return $this->foo; }
    /** @param string $foo */
    public function setFoo($foo) { $this->foo = $foo; }

    /** @var int */
    private $bar;
    /** @return int */
    public function getBar() { return $this->bar; }
    /** @param int $bar */
    public function setBar($bar) { $this->bar = $bar; }

    /** @var int */
    private $baz;
    /** @return int */
    public function getBaz() { return $this->baz; }
    /** @param int $baz */
    public function setBaz($baz) { $this->baz = $baz; }

    /** @var FooBar */
    private $qux;
    /** @return FooBar */
    public function getQux() { return $this->qux; }
    /** @var FooBar $qux */
    public function setQux($qux) { $this->qux = $qux; }

    /** @var FooBar */
    private $quux;
    /** @return FooBar */
    public function getQuux() { return $this->quux; }
    /** @var FooBar $quux */
    public function setQuux($quux) { $this->quux = $quux; }

    /** @var string */
    private $corge;
    /** @return string */
    public function getCorge() { return $this->corge; }
    /** @var string $corge */
    public function setCorge($corge) { $this->corge = $corge; }

    /** @var int[] */
    private $grault;
    /** @return int[] */
    public function getGrault() { return $this->grault; }
    /** @var int[] $grault */
    public function setGrault($grault) { $this->grault = $grault; }

    /** @var FooBar[] */
    private $garply;
    /** @return FooBar[] */
    public function getGarply() { return $this->garply; }
    /** @var string FooBar[] */
    public function setGarply($garply) { $this->garply = $garply; }

    /** @var FooBar */
    private $waldo;
    /** @return FooBar */
    public function getWaldo() { return $this->waldo; }
    /** @var FooBar $waldo */
    public function setWaldo($waldo) { $this->waldo = $waldo; }

    /** @var string */
    private $fred;
    /** @return string */
    public function getFred() { return $this->fred; }
    /** @var string $fred */
    public function setFred($fred) { $this->fred = $fred; }

    /** @var string */
    private $plugh;
    /** @return string */
    public function getPlugh() { return $this->plugh; }
    /** @var string $plugh */
    public function setPlugh($plugh) { $this->plugh = $plugh; }

    /** @var string */
    private $xyzzy;
    /** @return string */
    public function getXyzzy() { return $this->xyzzy; }
    /** @var string $xyzzy */
    public function setXyzzy($xyzzy) { $this->xyzzy = $xyzzy; }

    /** @var string */
    private $thud;
    /** @return string */
    public function getThud() { return $this->thud; }
    /** @var string $thud */
    public function setThud($thud) { $this->thud = $thud; }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
            'baz' => $this->baz,
            'qux' => $this->qux,
            'quux' => $this->quux,
            'corge' => $this->corge,
            'grault' => $this->grault,
            'garply' => $this->garply,
            'waldo' => $this->waldo,
            'fred' => $this->fred,
            'plugh' => $this->plugh,
            'xyzzy' => $this->xyzzy,
            'thud' => $this->thud
        ];
    }
}
