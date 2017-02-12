<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\TypeCast\Strategy\Mapper\Type\ToEncoding\ToEncodingMapperTypeInterface;

interface ToEncodingMapperInterface extends MapperStrategyInterface
{

    /**
     * @param ToEncodingMapperTypeInterface $encoding
     * @return $this
     */
    public function setEncoding( ToEncodingMapperTypeInterface $encoding );

}
