<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase;

class CamelCase implements ToCaseMapperTypeInterface
{

    const TYPE = 'mapper:to:case:camel';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
