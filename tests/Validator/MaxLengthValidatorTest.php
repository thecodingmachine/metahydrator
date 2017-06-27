<?php
namespace MetaHydratorTest\Validator;

use MetaHydrator\Validator\MaxLengthValidator;

class MaxLengthValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MaxLengthValidator */
    private $validator;

    protected function setup()
    {
        $this->validator = new MaxLengthValidator(25);
    }

    public function testValidate()
    {
        $this->validator->validate('lorem ipsum');
    }

    public function testValidateNull()
    {
        $this->validator->validate(null);
        $this->validator->validate('');
    }

    /**
     * @expectedException \MetaHydrator\Exception\ValidationException
     */
    public function testValidateThrow()
    {
        $this->validator->validate('Enlightenment fears when you study with advice.');
    }
}
