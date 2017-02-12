<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\ToCaseMapperTypeInterface;

interface ToCaseMapperInterface extends MapperStrategyInterface
{

    /**
     * @param ToCaseMapperTypeInterface $caseMode
     * @return $this
     */
    public function setCase( ToCaseMapperTypeInterface $caseMode );

}