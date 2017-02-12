<?php

namespace PhpObjects\DataSource\Sql\ConnectionId;

interface ConnectionIdFactoryInterface
{

    /**
     * @param string $jsonConnectionId
     * @return null|MySqlConnectionId
     */
    public function createFromJson( $jsonConnectionId );

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $port
     * @return MySqlConnectionId
     */
    public function createMySqlConnectionId( $host, $user, $password, $database, $port = MySqlConnectionId::DEFAULT_PORT );

}
