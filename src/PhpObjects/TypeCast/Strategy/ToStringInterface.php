<?php

namespace PhpObjects\TypeCast\Strategy;

use PhpObjects\TypeCast\Strategy\StrategyTrait\MapperTraitInterface;
use PhpObjects\TypeCast\Strategy\Type\ToString\ToStringTypeInterface;

interface ToStringInterface extends StrategyInterface, MapperTraitInterface
{

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param ToStringTypeInterface $stringType
     * @return $this
     */
    public function setFormat( ToStringTypeInterface $stringType );

    /**
     * @param mixed $data
     * @return string
     */
    public function cast( $data );

}
