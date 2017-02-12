<?php

namespace PhpObjects\TypeCast;

use PhpObjects\TypeCast\Strategy;
use PhpObjects\TypeCast\Strategy\Mapper;

class DataType
{

    private $_data;

    /**
     * @var DataCastStrategyInterface
     */
    private $_strategy;

    public function __construct()
    {
    }

    /**
     * @param  mixed $fromData
     * @param  DataCastStrategyInterface $toStrategy
     * @return mixed
     */
    public function cast( $fromData, DataCastStrategyInterface $toStrategy )
    {
        $this->_data = $fromData;
        $this->_strategy = $toStrategy;

        foreach ( $this->_strategy->getStrategyList() as $type => $strategy ) {
            $this->_data = $strategy->cast($this->_data);
        }

        return $this->_data;
    }

}
