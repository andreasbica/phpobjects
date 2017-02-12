<?php

namespace PhpObjects\TypeCast;

use PhpObjects\TypeCast\Strategy\StrategyInterface;

class DataCastStrategy implements DataCastStrategyInterface
{

    private $_strategyListe;


    public function __construct()
    {
        $this->resetStrategyList();
    }

    /**
     * @inheritdoc
     */
    public function addStrategy( StrategyInterface $strategy )
    {
        $this->_strategyListe[ (string) $strategy ] = $strategy;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStrategyList()
    {
        return $this->_strategyListe;
    }

    /**
     * @inheritdoc
     */
    public function resetStrategyList()
    {
        $this->_strategyListe = [];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterStrategyByType( $condition, $getFirstOnly = false )
    {
        $found = array_filter($this->getStrategyList(), function ( $strategy ) use ( $condition ) {
            if ( strpos((string) $strategy, $condition) !== false ) {
                return $strategy;
            }
        });
        return ($found ? ($getFirstOnly ? reset($found) : $found) : ($getFirstOnly ? null : []));
    }

}
