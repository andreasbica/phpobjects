<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToJson;

class HexApos implements ToJsonTypeInterface
{

    const TYPE = JSON_HEX_APOS;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
