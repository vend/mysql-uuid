<?php

namespace MysqlUuid;

use InvalidArgumentException;
use MysqlUuid\Formats\Format;
use MysqlUuid\Formats\PlainString;

/**
 * MySQL UUID format utilities
 */
class Uuid
{
    /**
     * The current UUID value under consideration
     *
     * @var string
     */
    protected $value;

    /**
     * @var Format
     */
    protected $format;

    /**
     * @var array<string,string>
     */
    protected $fields;

    /**
     * @param string $value  A UUID in any of the accepted formats
     * @param Format $format The format of the UUID (will be validated)
     */
    public function __construct($value, Format $format = null)
    {
        $this->value = $value;

        if ($format) {
            $this->format = $format;
        } else {
            $this->format = new PlainString();
        }
    }

    /**
     * Parses the value into fields (according to format)
     *
     * @throws InvalidArgumentException
     * @return void
     */
    protected function parse()
    {
        if (!isset($this->fields)) {
            $this->fields = $this->format->toFields($this->value);
        }

        if (!$this->fields) {
            throw new InvalidArgumentException('Cannot parse value to fields');
        }
    }

    /**
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public function getField($name)
    {
        $this->parse();
        return $this->fields[$name];
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function setField($name, $value)
    {
        $this->parse();
        $this->fields[$name] = $value;
    }

    /**
     * Checks whether the UUID appears valid for the specified input format
     *
     * The input format is set as the second constructor parameter. This method
     * will validate the $value passed to the constructor against the format.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->format->isValid($this->value);
    }

    /**
     * Converts the UUID to the specified format
     *
     * @param Format $format
     * @return string
     * @throws InvalidArgumentException
     */
    public function toFormat(Format $format)
    {
        $this->parse();
        return $format->fromFields($this->fields);
    }
}
