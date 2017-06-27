<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\ParsingException;

/**
 * An implementation of ParserInterface used to reconstruct DateTime objects from a string
 */
class DateTimeParser extends AbstractParser
{
    /** @var null|string */
    private $format;
    public function getFormat() { return $this->format; }
    public function setFormat(string $format) { $this->format = $format; }

    /** @var bool */
    private $ignoreWarnings;
    public function getIgnoreWarnings() { return $this->ignoreWarnings; }
    public function setIgnoreWarnings(bool $ignoreWarnings) { $this->ignoreWarnings = $ignoreWarnings; }

    /**
     * DateTimeParser constructor.
     * @param string|null $format
     * @param bool $ignoreWarnings
     * @param string $errorMessage
     */
    public function __construct(string $format = null, bool $ignoreWarnings = false, string $errorMessage = "")
    {
        parent::__construct($errorMessage);
        $this->format = $format;
        $this->ignoreWarnings = $ignoreWarnings;
    }

    /**
     * @param mixed $rawValue
     * @return \DateTime
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        }

        if ($this->format) {
            $date = \DateTime::createFromFormat($this->format, $rawValue);
        } else {
            $date = new \DateTime($rawValue);
        }

        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0) {
            $this->throw();
        }
        if (!$this->ignoreWarnings && $errors['warning_count'] > 0) {
            $this->throw();
        }

        return $date;
    }
}
