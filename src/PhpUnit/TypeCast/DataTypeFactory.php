<?php

namespace PhpObjects\TypeCast;

class DataTypeFactory implements DataTypeFactoryInterface
{

    public function __construct()
    {
    }

    /**
     * @param  mixed $data
     * @param  DataCastStrategyInterface $strategy
     * @return DataType
     */
    public function create( $data, DataCastStrategyInterface $strategy )
    {
        return new DataType($data, $strategy);
    }

}
