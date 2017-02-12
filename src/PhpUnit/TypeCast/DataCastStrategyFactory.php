<?php

namespace PhpObjects\TypeCast;

class DataCastStrategyFactory implements DataCastStrategyFactoryInterface
{

    public function __construct()
    {}

    /**
     * @return DataCastStrategyInterface
     */
    public function create()
    {
        return new DataCastStrategy();
    }

}
