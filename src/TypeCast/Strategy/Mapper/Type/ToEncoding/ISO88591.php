<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\Type\ToEncoding;

class ISO88591 implements ToEncodingMapperTypeInterface
{

    const TYPE = 'mapper:to:encoding:ISO-8859-1';

    public function __toString()
    {
        return self::TYPE;
    }

}
