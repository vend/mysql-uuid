<?php

namespace MysqlUuid;

use MysqlUuid\Formats\Format;
use MysqlUuid\Formats\String;
use UnexpectedValueException;
use LogicException;
use InvalidArgumentException;

/**
 * MySQL UUID format utilities
 *
 * UUID variant 1 format:
 *  Field                     Type            Octet  Note
 *  -----                     ----            -----  ----
 *  time_low                  unsigned long   0-3    The low field of the timestamp.
 *  time_mid                  unsigned short  4-5    The middle field of the timestamp.
 *  time_hi_and_version       unsigned short  6-7    The high field of the timestamp multiplexed with the version number.
 *  clock_seq_hi_and_reserved unsigned small  8      The high field of the clock sequence multiplexed with the variant.
 *  clock_seq_low             unsigned small  9      The low field of the clock sequence.
 *  node                      character       10-15  The spatially unique node identifier.
 */
class MysqlUuid
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

    protected $reorder = false;

    /**
     * @param string  $value   A UUID in any of the accepted formats
     * @param Format  $format  The format of the UUID (will be validated)
     * @param boolean $reorder Whether to reorder fields within the UUID as an optimization
     */
    public function __construct($value, Format $format = null, $reorder = false)
    {
        $this->value = $value;

        if ($format) {
            $this->format = $format;
        } else {
            $this->format = new String();
        }

        $this->reorder = $reorder;
    }

    public function isValid()
    {
        return $this->format->isValid($this->value);
    }

    public function toFormat(Format $format)
    {
        return $format->fromFields($this->format->toFields($this->value));
    }






    /**
     * @throws LogicException
     * @return string 16 byte binary string
     */
    public function toBinary()
    {
        switch ($this->format) {
            case self::FORMAT_HEX:
                return pack('H*', $this->value);
        }
    }





    /**
     * @param string  $binary 16 bytes
     * @param boolean $strict Whether to throw an exception on an invalid UUID input
     * @throws InvalidArgumentException On invalid input
     * @return string 36 characters, hex with dashes
     */
    public static function binaryToUuid($binary, $strict = false)
    {
        if ($strict && !self::isBinary($binary)) {
            throw new InvalidArgumentException('Invalid binary UUID, could not convert to UUID value');
        }

        $h = unpack(self::UNPACK_FORMAT, $binary);
        return sprintf('%s-%s-%s-%s-%s', $h['time_low'], $h['time_mid'], $h['time_high'], $h['clock_seq'], $h['node']);
    }

    /**
     * @param string  $binary 16 bytes
     * @param boolean $strict Whether to throw an exception on an invalid UUID input
     * @throws InvalidArgumentException On invalid input
     * @return string 32 character, hex, no dashes
     */
    public static function binaryToHex($binary, $strict = false)
    {
        if ($strict && !self::isBinary($binary)) {
            throw new InvalidArgumentException('Invalid binary UUID, could not convert to hex value');
        }

        $h = unpack(self::UNPACK_FORMAT, $binary);
        return implode('', $h);
    }


    /**
     * @param string  $hex    32 characters, no dashes, optimized order
     * @param boolean $strict Whether to throw an exception on an invalid UUID input
     * @throws InvalidArgumentException On invalid input
     * @return string $uuid 36 characters hex, dashes, original order
     */
    public static function hexToUuid($hex, $strict = false)
    {
        if ($strict && !self::isHex($hex)) {
            throw new InvalidArgumentException('Invalid hex UUID, could not convert to UUID value');
        }

        return self::binaryToUuid(self::hexToBinary($hex));
    }
}
