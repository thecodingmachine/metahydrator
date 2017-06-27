<?php
namespace MetaHydratorTest\Validator;

use MetaHydrator\Validator\EnumValidator;

class EnumValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $enum = ['blue', 'red', 'magenta'];
        $this->validator = new EnumValidator($enum);
    }

    public function testValidate()
    {
        $this->validator->validate('red');
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
        $this->validator->validate('cyan');
    }
}
