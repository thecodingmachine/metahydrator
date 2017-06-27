<?php
namespace MetaHydratorTest\Validator;

use MetaHydrator\Validator\RegexValidator;

class RegexValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $pattern = '/^[\p{L} \'-]+$/ui';
        $this->validator = new RegexValidator($pattern);
    }

    public function testValidate()
    {
        $this->validator->validate('René Jacques');
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
        $this->validator->validate('André 3000');
    }
}
