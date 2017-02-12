<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToEncoding;

class UTF8 implements ToEncodingMapperTypeInterface
{

    const TYPE = 'mapper:to:encoding:UTF-8';

    public function __toString()
    {
        return self::TYPE;
    }

}
