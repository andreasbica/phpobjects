<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToJson;

class HexAmp implements ToJsonTypeInterface
{

    const TYPE = JSON_HEX_AMP;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
