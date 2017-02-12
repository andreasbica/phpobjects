<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToString;

class Unserialize implements ToStringTypeInterface
{

    const TYPE = 'to:unserialize';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
