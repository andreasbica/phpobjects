<?php

namespace PhpObjects\TypeCast;

interface DataTypeFactoryInterface
{

    /**
     * @param  mixed $data
     * @param  DataCastStrategyInterface $strategy
     * @return DataType
     */
    public function create( $data, DataCastStrategyInterface $strategy );

}
