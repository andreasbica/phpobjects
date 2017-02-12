<?php

namespace PhpObjects\TypeCast\Strategy;

use PhpObjects\TypeCast\Strategy\StrategyTrait\MapperTraitInterface;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromFormatTypeInterface;

interface ToArrayInterface extends StrategyInterface, MapperTraitInterface
{

    /**
     * @inheritdoc
     */
    public function __toString();

    /**
     * @param FromFormatTypeInterface $formatType
     */
    public function fromFormat( FromFormatTypeInterface $formatType );

    /**
     * @param mixed $data
     * @return array
     */
    public function cast( $data );

}
