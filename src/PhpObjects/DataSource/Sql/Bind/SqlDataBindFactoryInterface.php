<?php

namespace PhpObjects\DataSource\Sql\Bind;

interface SqlDataBindFactoryInterface
{

    /**
     * @param string $statement
     * @param array $binding
     * @return SqlDataBind
     */
    public static function create( $statement, array $binding );

}
