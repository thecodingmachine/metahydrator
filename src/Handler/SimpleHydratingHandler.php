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

    /**
     * SimpleHydratingHandler constructor.
     * @param string $key
     * @param ParserInterface $parser
     * @param ValidatorInterface[] $validators
     */
    public function __construct(string $key, ParserInterface $parser, array $validators = [])
    {
        $this->key = $key;
        $this->parser = $parser;
        $this->validators = $validators;
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
            $rawValue = null;
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
