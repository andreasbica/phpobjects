<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToObject;

class Generic implements ToObjectTypeInterface
{

    const TYPE = 'to:class:generic';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
