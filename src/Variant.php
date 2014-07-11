<?php

namespace MysqlUuid;

use InvalidArgumentException;

/**
 * Variant
 *
 * The variant field should be more accurately called the UUID 'type': it
 * determines the interpretation of all other parts of the UUID. It's the 'outer
 * value' of the version (i.e. the UUID variant has many versions: versions DO NOT
 * have variants).
 */
class Variant
{
    const NCS       = 0;
    const RFC4122   = 1;
    const MICROSOFT = 2;
    const FUTURE    = 3;
    const OTHER     = 4;

    /**
     * Named variants
     *
     * 'other' not named, because only used as fallback value
     *
     * @var array<int,string>
     */
    public static $variants = [
        self::NCS       => 'ncs',
        self::RFC4122   => 'rfc4122',
        self::MICROSOFT => 'microsoft',
        self::FUTURE    => 'future'
    ];

    /**
     * The mask for the variant is established as a single byte, and an int
     * from 1-8 that says how many bits of the byte value are considered
     * insignificant (the RFC says the multiplexing differs between variants)
     *
     * That is, if the variant uses the first 3 bits of the multiplexed value,
     * the int value will be 8 - 3 = 5. This means shifting the multiplexed value
     * left by five bits will retain only the variant information.
     *
     * @var array<int,array>
     */
    protected $mask = [
        self::NCS       => [0x00, 7],
        self::RFC4122   => [0x80, 6],
        self::MICROSOFT => [0xC0, 5],
        self::FUTURE    => [0xE0, 5]
    ];

    /**
     * @var Uuid
     */
    protected $uuid;

    /**
     * @param Uuid $uuid
     */
    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return int
     */
    public function get()
    {
        $value = ord($this->getVariantByte());

        foreach ($this->mask as $variant => $mask) {
            list($pattern, $bits) = $mask;

            $masked = $value >> $bits << $bits;

            if ($masked === $pattern) {
                return $variant;
            }
        }

        return self::OTHER;
    }

    /**
     * @param int $variant See self::* formats
     */
    public function set($variant)
    {
        if (empty($this->mask[$variant])) {
            throw new InvalidArgumentException('Invalid variant scheme; cannot find mask');
        }

        $byte = ord($this->getVariantByte());
        list($pattern, $bits) = $this->mask[$variant];

        $mask = 0xFF >> $bits << $bits;
        $value = ($byte & ~$mask) | ($pattern & $mask);

        $this->setVariantByte(chr($value));
    }

    /**
     * Get a single byte from the UUID containing the variant in the high bits
     *
     * We're assuming here that the variant will only ever be multiplexed into
     * a single byte of the clock_seq field. (True of all currently known
     * variants)
     *
     * @return string
     */
    protected function getVariantByte()
    {
        $bin = hex2bin($this->uuid->getField('clock_seq'));
        return $bin[0];
    }

    /**
     * @param string $byte A single byte
     * @return string
     */
    protected function setVariantByte($byte)
    {
        $bin = hex2bin($this->uuid->getField('clock_seq'));
        $bin[0] = $byte;
        $this->uuid->setField('clock_seq', bin2hex($bin));
    }
}
