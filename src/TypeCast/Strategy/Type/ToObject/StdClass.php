<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToObject;

class StdClass implements ToObjectTypeInterface
{

    const TYPE = 'to:class:std';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
