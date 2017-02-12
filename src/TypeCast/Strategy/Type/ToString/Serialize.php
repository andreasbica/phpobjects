<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToString;

class Serialize implements ToStringTypeInterface
{

    const TYPE = 'to:serialize';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
