<?php
namespace MetaHydrator\Handler;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ValidationException;
use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Parser\ParserInterface;
use MetaHydrator\Validator\ValidatorInterface;

/**
 * An implementation of HydratingHandlerInterface, as a one to one value parsing/validation.
 *
 * Class SimpleHydratingHandler
 * @package MetaHhydrator\Handler
 */
class SimpleHydratingHandler implements HydratingHandlerInterface
{
    /** @var string */
    private $key;
    public function getKey() { return $this->key; }
    public function setKey(string $key) { $this->key = $key; }

    /** @var ParserInterface */
    private $parser;
    public function getParser() { return $this->parser; }
    public function setParser(ParserInterface $parser) { $this->parser = $parser; }

    /** @var ValidatorInterface[] */
    private $validators;
    public function getValidators() { return $this->validators; }
    public function setValidators(array $validators) { $this->validators = $validators; }
    public function addValidator(ValidatorInterface $validator) { $this->validators[] = $validator; }

    /** @var mixed */
    private $defaultValue;
    public function getDefaultValue() { return $this->defaultValue; }
    public function setDefaultValue($defaultValue) { $this->defaultValue = $defaultValue; }

    /**
     * SimpleHydratingHandler constructor.
     * @param string $key
     * @param ParserInterface $parser
     * @param ValidatorInterface[] $validators
     * @param mixed $default
     */
    public function __construct(string $key, ParserInterface $parser, array $validators = [], $defaultValue = null)
    {
        $this->key = $key;
        $this->parser = $parser;
        $this->validators = $validators;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param array $data
     * @param array $targetData
     * @param $object
     *
     * @throws HydratingException
     */
    public function handle(array $data, array &$targetData, $object = null)
    {
        if (array_key_exists($this->key, $data)) {
            $rawValue = $data[$this->key];
        } else if ($object === null) {
            $rawValue = $this->defaultValue;
        } else {
            return;
        }

        try {
            $parsedValue = $this->parser->parse($rawValue);
        } catch (ParsingException $exception) {
            throw new HydratingException([ $this->key => $exception->getInnerError() ]);
        }

        $this->validate($parsedValue, $object);

        $targetData[$this->key] = $parsedValue;
    }

    /**
     * @param mixed $parsedValue
     * @param mixed $contextObject
     *
     * @throws HydratingException
     */
    private function validate($parsedValue, $contextObject = null)
    {
        try {
            foreach ($this->validators as $validator) {
                $validator->validate($parsedValue, $contextObject);
            }
        } catch (ValidationException $exception) {
            throw new HydratingException([ $this->key => $exception->getInnerError() ]);
        }
    }
}
