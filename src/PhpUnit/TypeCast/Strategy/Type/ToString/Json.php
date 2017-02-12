<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToString;

use PhpObjects\TypeCast\Strategy\Type\ToJson\ToJsonTypeInterface;

class Json implements ToStringTypeInterface
{

    const TYPE = 'to:json';

    private $_bitMask = 0;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @param ToJsonTypeInterface $bitMask
     * @return $this
     */
    public function setBitMask( ToJsonTypeInterface $bitMask )
    {
        $this->_bitMask = $bitMask;
        return $this;
    }

    /**
     * @return int
     */
    public function getBitMask()
    {
        return (int) (string) $this->_bitMask;
    }

}
