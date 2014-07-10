<?php

namespace MysqlUuid;

use InvalidArgumentException;
use MysqlUuid\Formats\Format;
use MysqlUuid\Formats\String;

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
            $this->format = new String();
        }

        $this->fields = $this->format->toFields($this->value);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setField($name, $value)
    {
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
        if (!is_array($this->fields)) {
            throw new InvalidArgumentException('Cannot get fields from UUID value');
        }

        return $format->fromFields($this->fields);
    }
}
