<?php

namespace PhpObjects\DataSource\Sql\Bind;

class SqlDataBindFactory implements SqlDataBindFactoryInterface
{

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public static function create( $statement, array $binding )
    {
        return new SqlDataBind($statement, $binding);
    }

}
