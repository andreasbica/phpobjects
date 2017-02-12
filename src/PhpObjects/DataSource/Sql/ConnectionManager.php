<?php

namespace PhpObjects\DataSource;

use PhpObjects\DataSource\Sql\Connector\ConnectorInterface;
use PhpObjects\DataSource\Sql\ConnectionId\ConnectionIdFactory;
use PhpObjects\DataSource\Sql\ConnectionId\ConnectionIdInterface;
use PhpObjects\DataSource\Sql\ConnectionId\MySqlConnectionId;
use PhpObjects\DataSource\Sql\Connector\MySqlConnector;

class ConnectionManager
{

    /**
     * @var array
     */
    private static $_ConnectionCollection = [];

    /**
     * @var ConnectionIdFactory
     */
    private $_connectionIdFactory;

    
    public function __construct()
    {
        $this->_connectionIdFactory = new ConnectionIdFactory();
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return static::$_ConnectionCollection;
    }

    /**
     * @param string $jsonConnectionId
     * @return ConnectorInterface | null
     */
    public function getConnectionFromJson( $jsonConnectionId )
    {
        $connectionId = $this->_connectionIdFactory->createFromJson($jsonConnectionId);

        if ( !$connectionId ) {
            return null;
        }

        return $this->getConnectionFromConnectionId($connectionId);
    }

    /**
     * @param ConnectionIdInterface $connectionId
     * @return ConnectorInterface | null
     */
    public function getConnectionFromConnectionId( ConnectionIdInterface $connectionId )
    {
        if ( $connectionId->getKey(ConnectionIdInterface::KEY_TYPE) == MySqlConnectionId::TYPE ) {
            return $this->_handleConnection((new MySqlConnector($connectionId)));
        }

        return null;
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $port
     * @return ConnectorInterface | null
     */
    public function getMySqlConnection( $host, $user, $password, $database, $port = MySqlConnectionId::DEFAULT_PORT )
    {
        $connectionId = $this->_connectionIdFactory->createMySqlConnectionId($host, $user, $password, $database, $port);
        return $this->getConnectionFromConnectionId($connectionId);
    }

    /**
     * @param ConnectorInterface $connector
     * @return mixed
     */
    private function _handleConnection( ConnectorInterface $connector )
    {
        $connectionId = (string) $connector->getConnectionId();

        if ( array_key_exists($connectionId, static::$_ConnectionCollection) ) {
            return static::$_ConnectionCollection[ $connectionId ];
        }

        $isSuccess = $connector->openConnection();

        static::$_ConnectionCollection[ $connectionId ] = null;

        if ( $isSuccess ) {
            static::$_ConnectionCollection[ $connectionId ] = $connector;
        }

        return static::$_ConnectionCollection[ $connectionId ];
    }

}
