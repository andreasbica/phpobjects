<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase;

class UpperCaseFirst implements ToCaseMapperTypeInterface
{

    const TYPE = 'mapper:to:case:first';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
