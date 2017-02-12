<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\TypeCast\Strategy\Mapper\MapperTrait\KeyAndValueTraitInterface;

interface MapperStrategyInterface extends KeyAndValueTraitInterface
{

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param mixed $data
     * @return array | string
     */
    public function cast( $data );

}