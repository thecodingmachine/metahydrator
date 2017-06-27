<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Exception\ValidationException;
use MetaHydrator\Validator\ValidatorInterface;

/**
 * Class ArrayParser
 * @package MetaHydrator\Parser
 */
class ArrayParser extends AbstractParser
{
    /** @var ParserInterface */
    private $subParser;
    public function getSubParser() { return $this->subParser; }
    public function setSubParser(ParserInterface $parser) { $this->subParser = $parser; }

    /** @var ValidatorInterface[] */
    private $subValidators;
    public function getSubValidators() { return $this->subValidators; }
    public function setSubValidators(array $validators) { $this->subValidators = $validators; }
    public function addSubValidator(ValidatorInterface $validator) { $this->subValidators[] = $validator; }

    /**
     * ArrayParser constructor.
     * @param ParserInterface $subParser
     * @param ValidatorInterface[] $subValidators
     * @param string $errorMessage
     */
    public function __construct(ParserInterface $subParser, array $subValidators = [], string $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->subParser = $subParser;
        $this->subValidators = $subValidators;
    }

    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }
        if (!is_array($rawValue)) {
            $this->throw();
        }
        $errors = [];
        $ko = false;
        $parsedArray = [];
        foreach ($rawValue as $key => $value) {
            try {
                $parsedValue = $this->subParser->parse($value);
                $this->validate($parsedValue);
                $parsedArray[$key] = $parsedValue;
                $errors[$key] = null;
            } catch (ValidationException $exception) {
                $errors[$key] = $exception->getInnerError();
                $ko = true;
            }
        }

        if ($ko) {
            throw new ParsingException($errors);
        }

        return $parsedArray;
    }

    /**
     * @param $subValue
     * @throws ValidationException
     */
    private function validate($subValue)
    {
        foreach ($this->subValidators as $subValidator) {
            $subValidator->validate($subValue);
        }
    }
}
