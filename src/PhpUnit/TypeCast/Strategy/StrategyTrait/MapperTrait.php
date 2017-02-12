<?php

namespace PhpObjects\TypeCast\Strategy\StrategyTrait;

use PhpObjects\TypeCast\Strategy\Mapper\MapperStrategyInterface;
use PhpObjects\TypeCast\Strategy\ToArray;
use PhpObjects\TypeCast\Strategy\ToString;

trait MapperTrait
{

    /**
     * @var MapperStrategyInterface[]
     */
    private $_mapperList;

    /**
     * @inheritdoc
     */
    public function addMapper( MapperStrategyInterface $mapper )
    {
        switch (static::TYPE) {
            case ToArray::TYPE:
                ($mapper->mapKeyOnly() || $mapper->mapValueOnly() ?: $mapper->mapKeyOnly());
                break;

            case ToString::TYPE:
                $mapper->mapValueOnly();
                break;
        }

        $this->_mapperList[] = $mapper;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMapper()
    {
        return $this->_mapperList;
    }

    /**
     * @inheritdoc
     */
    public function resetMapper()
    {
        $this->_mapperList = [];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterMapperByType( $typeCondition, $getFirstOnly = false )
    {
        $found = array_filter($this->getMapper(), function ( $mapper ) use ( $typeCondition ) {
            if ( strpos((string) $mapper, $typeCondition) !== false ) {
                return $mapper;
            }
        });
        return ($found ? ($getFirstOnly ? reset($found) : $found) : ($getFirstOnly ? null : []));
    }

}
