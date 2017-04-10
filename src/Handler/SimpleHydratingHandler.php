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

    /** @var ParserInterface */
    private $parser;

    /** @var ValidatorInterface[] */
    private $validators;

    /**
     * SimpleHydratingHandler constructor.
     * @param string $key
     * @param ParserInterface $parser
     * @param ValidatorInterface[] $validators
     */
    public function __construct($key, $parser, $validators = [])
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
            $targetData[$this->key] = $this->parser->parse($rawValue);
        } catch (ParsingException $exception) {
            throw new HydratingException([ $this->key => $exception->getInnerError() ]);
        }

        $this->validate($targetData, $object);
    }

    /**
     * @param array $parsedData
     * @param mixed $contextObject
     *
     * @throws HydratingException
     */
    private function validate($parsedData, $contextObject = null)
    {
        try {
            foreach ($this->validators as $validator) {
                $validator->validate($parsedData[$this->key], $contextObject);
            }
        } catch (ValidationException $exception) {
            throw new HydratingException([ $this->key => $exception->getInnerError() ]);
        }
    }
}
