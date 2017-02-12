<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToJson;

class HexTag implements ToJsonTypeInterface
{

    const TYPE = JSON_HEX_TAG;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
