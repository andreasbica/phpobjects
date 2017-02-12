<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToArray;

class FromJson implements FromFormatTypeInterface
{

    const TYPE = 'from:json';

    public function __construct()
    {}

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

}
