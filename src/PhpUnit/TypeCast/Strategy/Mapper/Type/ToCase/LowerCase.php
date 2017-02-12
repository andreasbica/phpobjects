<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase;

class LowerCase implements ToCaseMapperTypeInterface
{

    const TYPE = 'mapper:to:case:lower';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
