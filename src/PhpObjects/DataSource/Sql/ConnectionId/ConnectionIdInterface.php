<?php

namespace PhpObjects\DataSource\Sql\ConnectionId;

interface ConnectionIdInterface
{

    const KEY_TYPE = 'type';
    
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $key
     * @return string | null
     */
    public function getKey( $key );

    /**
     * @return array
     */
    public function castToArray();

}
