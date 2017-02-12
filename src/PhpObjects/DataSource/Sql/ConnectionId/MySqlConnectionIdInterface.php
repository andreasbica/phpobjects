<?php

namespace PhpObjects\DataSource\Sql\ConnectionId;

interface MySqlConnectionIdInterface extends ConnectionIdInterface
{
    
    const TYPE = 'mysql';

    const DEFAULT_PORT = '3306';

    const KEY_HOST = 'host';
    const KEY_USER = 'user';
    const KEY_PASSWORD = 'password';
    const KEY_DATABASE = 'database';
    const KEY_PORT = 'port';

}
