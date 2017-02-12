<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase;

class UpperCase implements ToCaseMapperTypeInterface
{

    const TYPE = 'mapper:to:case:upper';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
