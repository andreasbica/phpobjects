<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToJson;

class ForceObject implements ToJsonTypeInterface
{

    const TYPE = JSON_FORCE_OBJECT;

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
