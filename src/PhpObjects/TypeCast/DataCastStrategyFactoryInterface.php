<?php

namespace PhpObjects\TypeCast;

interface DataCastStrategyFactoryInterface
{

    /**
     * @return DataCastStrategyInterface
     */
    public function create();
    
}
