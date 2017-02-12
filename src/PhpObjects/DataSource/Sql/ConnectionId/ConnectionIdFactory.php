<?php

namespace PhpObjects\DataSource\Sql\ConnectionId;

class ConnectionIdFactory implements ConnectionIdFactoryInterface
{

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function createFromJson( $jsonConnectionId )
    {
        $connectionData = json_decode($jsonConnectionId, true);
        (is_array($connectionData) ?: $connectionData = []);

        if ( $connectionData && isset($connectionData[ 'type' ]) ) {
            switch ($connectionData[ 'type' ]) {
                case MySqlConnectionId::TYPE:
                    return $this->createMySqlConnectionId(
                        $connectionData[ MySqlConnectionId::KEY_HOST ],
                        $connectionData[ MySqlConnectionId::KEY_USER ],
                        $connectionData[ MySqlConnectionId::KEY_PASSWORD ],
                        $connectionData[ MySqlConnectionId::KEY_DATABASE ],
                        (isset($connectionData[ MySqlConnectionId::KEY_PORT ])
                            ? $connectionData[ MySqlConnectionId::KEY_PORT ]
                            : MySqlConnectionId::DEFAULT_PORT
                        )
                    );
                    break;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function createMySqlConnectionId( $host, $user, $password, $database, $port = MySqlConnectionId::DEFAULT_PORT )
    {
        return new MySqlConnectionId($host, $user, $password, $database, $port);
    }

}
