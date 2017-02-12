<?php

namespace PhpObjects\TypeCast;

use PhpObjects\TypeCast\Strategy\StrategyInterface;

interface DataCastStrategyInterface
{

    /**
     * @param StrategyInterface $strategy
     * @return $this
     */
    public function addStrategy( StrategyInterface $strategy );

    /**
     * @return StrategyInterface[]
     */
    public function getStrategyList();

    /**
     * @return $this
     */
    public function resetStrategyList();

    /**
     * @param string $typeCondition
     * @param bool $getFirstOnly
     * @return StrategyInterface[] | StrategyInterface | null
     */
    public function filterStrategyByType( $typeCondition, $getFirstOnly = false );

}
