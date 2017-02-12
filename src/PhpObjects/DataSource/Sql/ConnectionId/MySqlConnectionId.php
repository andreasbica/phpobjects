<?php

namespace PhpObjects\DataSource\Sql\ConnectionId;

class MySqlConnectionId implements MySqlConnectionIdInterface
{

    /**
     * @var array
     */
    private $_connectionIdData = [];


    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $port
     */
    public function __construct( $host, $user, $password, $database, $port = self::DEFAULT_PORT )
    {
        $this->_connectionIdData = $this->_castToArray($host, $user, $password, $database, $port);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return json_encode($this->castToArray());
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return (string) $this;
    }

    /**
     * @inheritdoc
     */
    public function castToArray()
    {
        return $this->_connectionIdData;
    }

    /**
     * @inheritdoc
     */
    public function getKey( $key )
    {
        if ( array_key_exists($key, $this->_connectionIdData) ) {
            return $this->_connectionIdData[ $key ];
        }
        return null;
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $port
     * @return array
     */
    private function _castToArray( $host, $user, $password, $database, $port )
    {
        return [
            self::KEY_TYPE => self::TYPE,
            self::KEY_HOST => $host,
            self::KEY_USER => $user,
            self::KEY_PASSWORD => $password,
            self::KEY_DATABASE => $database,
            self::KEY_PORT => $port
        ];
    }

}
