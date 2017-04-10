<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Handler\HydratingHandlerInterface;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Validator\ValidatorInterface;
use Mouf\Hydrator\Hydrator;
use Mouf\Hydrator\TdbmHydrator;

/**
 * A custom class parser based on the MetaHydrator behaviour
 *
 * Class ObjectParser
 * @package MetaHydrator\Parser
 */
class ObjectParser extends AbstractParser implements ParserInterface
{
    /** @var string */
    private $className;
    public function getClassName() { return $this->className;}
    public function setClassName(string $className) { $this->className = $className; }

    /** @var Hydrator */
    private $hydrator;
    public function getHydrator() { return $this->hydrator; }
    public function setHydrator(Hydrator $hydrator) { $this->hydrator = $hydrator; }

    /**
     * ObjectParser constructor.
     * @param string $className
     * @param Hydrator $hydrator
     * @param string $errorMessage
     */
    public function __construct(string $className, Hydrator $hydrator, string $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->className = $className;
        $this->hydrator = $hydrator;
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

        try {
            return $this->hydrator->hydrateNewObject($rawValue, $this->className);
        } catch (HydratingException $exception) {
            throw new ParsingException($exception->getErrorsMap());
        }
    }
}
