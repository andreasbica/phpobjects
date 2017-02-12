<?php

namespace PhpObjects\TypeCast\Strategy\StrategyTrait;

use PhpObjects\TypeCast\Strategy\Mapper\MapperStrategyInterface;

interface MapperTraitInterface
{

    /**
     * @param MapperStrategyInterface $mapper
     * @return $this
     */
    public function addMapper( MapperStrategyInterface $mapper );

    /**
     * @return MapperStrategyInterface[]
     */
    public function getMapper();

    /**
     * @return $this
     */
    public function resetMapper();

    /**
     * @param string $typeCondition
     * @param bool $getFirstOnly
     * @return MapperStrategyInterface[] | MapperStrategyInterface | null
     */
    public function filterMapperByType( $typeCondition, $getFirstOnly = false );

}
