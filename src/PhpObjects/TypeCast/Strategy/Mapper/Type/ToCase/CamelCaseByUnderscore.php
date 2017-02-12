<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase;

class CamelCaseByUnderscore implements ToCaseMapperTypeInterface
{

    const TYPE = 'mapper:to:case:byunderscore';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
