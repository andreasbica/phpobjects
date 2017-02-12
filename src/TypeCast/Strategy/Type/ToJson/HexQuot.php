<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToJson;

class HexQuot implements ToJsonTypeInterface
{

    const TYPE = JSON_HEX_QUOT;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
