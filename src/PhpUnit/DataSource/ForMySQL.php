<?php

namespace PhpObjects\DataSource;

use PhpObjects\DataSource\Bind\Data;
use PhpObjects\DataSource\Bind\Column;
use PhpObjects\DataSource\Bind\OrderClause;
use PhpObjects\DataSource\Bind\WhereClause;

abstract class ForMySQL
{

    /**
     * @var \mysqli
     */
    private static $_connection = null;

    /**
     * @var string
     */
    private static $_connectionString = '';

    /**
     * @param  string $host
     * @param  string $user
     * @param  string $password
     * @param  string $database
     * @param  string $port
     * @return bool
     */
    public static function openConnection( $host, $user, $password, $database, $port = '3306' )
    {
        self::$_connectionString = "host=$host; username=$user; passwd=$password; dbname=$database; post=$port";
        self::$_connection = new \mysqli($host, $user, $password, $database, $port);

        if ( self::$_connection->connect_error ) {
            self::$_connection = null;
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function openDefaultConnection()
    {
        $host = 'localhost';
        $port = '3306';
        $user = 'root';
        $password = '';
        $database = 'test';

        return self::openConnection($host, $user, $password, $database, $port);
    }

    /**
     * Set the db connection to be use.
     * @param  \mysqli $connection
     * @return bool
     */
    public static function setConnection( \mysqli $connection )
    {
        if ( $connection->connect_error ) {
            return false;
        }

        self::$_connection = $connection;
        self::$_connectionString = '@external';

        return true;
    }

    public static function unsetConnection()
    {
        if ( self::$_connection ) {
            self::$_connection->close();
        }

        self::$_connectionString = '';
        self::$_connection = null;
    }

    /**
     * @return \mysqli
     */
    public static function getConnection()
    {
        return self::$_connection;
    }

    /**
     * @return string
     */
    public static function getConnectionString()
    {
        return self::$_connectionString;
    }

    /**
     * @param string $sql
     * @param array $dataBindList
     * @param bool $onlyFirstRow
     * @param int $case
     * @return array | bool
     * @throws \Exception Any inner exceptions will be caught and rethrown
     */
    public static function execute( $sql, array $dataBindList = array(), $onlyFirstRow = false, $case = null )
    {
        if ( $dataBindList ) {
            $sql = SqlBound::create($sql, $dataBindList)->getSql(true);
        }

        if ( !self::getConnection() ) {
            if ( !self::openDefaultConnection() ) {
                throw new \Exception('Connection to mysql failed.');
            }
        }

        $sqlResult = self::getConnection()->query($sql, MYSQLI_USE_RESULT);

        if ( is_object($sqlResult) ) {
            if ( $onlyFirstRow ) {
                $dataList = $sqlResult->fetch_assoc();
            } else {
                $dataList = $sqlResult->fetch_all(MYSQLI_ASSOC);
            }

            if ( $dataList && ($case === CASE_UPPER || $case === CASE_LOWER) ) {
                $dataList = self::_sqlResultPrepare($dataList, $case);
            }

            $sqlResult->free_result();

            return $dataList;
        }

        if ( $sqlResult === false ) {
            throw new \Exception('Unable to run mysql query [' . $sql . '].');
        }

        return $sqlResult;
    }

    /**
     * @param SqlBound $boundSql SQL an binding to be executed
     * @param bool $onlyFirstRow wether or not only the first result shall be returned
     * @param int $case (CASE_UPPER|CASE_LOWER)
     *
     * @return array
     * @throws \Exception
     */
    final public static function executeBound( SqlBound $boundSql, $onlyFirstRow = false, $case = CASE_LOWER )
    {
        $sql = $boundSql->getSql(true);
        return self::execute($sql, [], $onlyFirstRow, $case);
    }

    /**
     * @return int
     */
    public static function getNextSequenceValue()
    {
        return (mysql_insert_id(self::$_connection) + 1);
    }

    /**
     * @return int
     */
    public static function getLastInsertId()
    {
        return mysql_insert_id(self::$_connection);
    }

    /**
     * @param  string $table
     * @param  int $case CASE_UPPER | CASE_LOWER
     * @return array
     */
    public static function getDescribeTable( $table, $case = CASE_LOWER )
    {
        $result = self::execute('describe ' . $table, [], false, $case);

        if ( $result === false ) {
            return [];
        }

        return $result;
    }

    /**
     * @param string $schema
     * @param string $table
     * @param Column $columnBind
     * @param WhereClause $whereClauseBind
     * @param OrderClause $orderClauseBind
     * @param array $rowCount
     * @return SqlBound
     */
    public static function buildSelectStatement(
        $schema,
        $table,
        Column $columnBind = null,
        WhereClause $whereClauseBind = null,
        OrderClause $orderClauseBind = null,
        array $rowCount = [0, 0]
    )
    {
        $fieldNameList = ($columnBind ? $columnBind->getBinding() : []);
        $fieldlistString = (empty($fieldNameList)) ? '*' : implode(', ', $fieldNameList);
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);

        $sql = "SELECT $fieldlistString FROM $tableString";
        $binding = [];

        if ( ($whereSql = ($whereClauseBind ? $whereClauseBind->getSqlBound()->getSql() : '')) ) {
            $binding = $whereClauseBind->getSqlBound()->getBinding();
            $sql .= $whereSql;
        }

        if ( ($orderSql = ($orderClauseBind ? $orderClauseBind->getSqlResolved() : '')) ) {
            $sql .= $orderSql;
        }

        $boundSql = SqlBound::create($sql, $binding);
        $boundSql = self::_appendRownumClause($boundSql, $rowCount);

        return $boundSql;
    }

    /**
     * @param string $schema
     * @param string $table
     * @param Data $dataBind
     * @return SqlBound
     */
    public static function buildInsertStatement( $schema, $table, Data $dataBind )
    {
        $keys = $insertSql = '';
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $dataSqlBound = $dataBind->getSqlBound(Data::SQL_OPERATION_INSERT);
        $binding = $dataSqlBound->getBinding();

        if ( $dataSqlBound ) {
            $keys = implode(', ', array_keys($dataBind->getBinding()));
            $insertSql = $dataSqlBound->getSql();
        }

        $sql = "INSERT INTO $tableString (" . $keys . ') VALUES (' . $insertSql . ')';

        return new SqlBound($sql, $binding);
    }

    /**
     * @param string $schema
     * @param string $table
     * @param Data $dataBind
     * @param WhereClause $whereClauseBind
     * @return SqlBound
     */
    public static function buildUpdateStatement( $schema, $table, Data $dataBind, WhereClause $whereClauseBind = null )
    {
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $dataSqlBound = $dataBind->getSqlBound(Data::SQL_OPERATION_UPDATE);

        $sql = "UPDATE $tableString SET ";
        $sql .= $dataSqlBound->getSql();

        $binding = $dataSqlBound->getBinding();

        if ( $whereClauseBind ) {
            $sql .= $whereClauseBind->getSqlBound()->getSql();
            $binding = array_merge($binding, $whereClauseBind->getSqlBound()->getBinding());
        }

        return new SqlBound($sql, $binding);
    }

    /**
     * @param string $schema
     * @param string $table
     * @param WhereClause $whereClauseBind
     * @return SqlBound
     */
    public static function buildDeleteStatement( $schema, $table, WhereClause $whereClauseBind = null )
    {
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $sql = "DELETE FROM $tableString";
        $binding = [];

        if ( $whereClauseBind ) {
            $sql .= $whereClauseBind->getSqlBound()->getSql();
            $binding = $whereClauseBind->getSqlBound()->getBinding();
        }

        return new SqlBound($sql, $binding);
    }

    /**
     * @param  SqlBound $boundSql
     * @param  array $rowCount
     * @return SqlBound
     */
    private static function _appendRownumClause( SqlBound $boundSql, array $rowCount )
    {
        $limit = '';

        if ( is_array($rowCount) ) {
            if ( array_key_exists(0, $rowCount) && $rowCount[ 0 ] != 0 ) {
                $limit .= $rowCount[ 0 ];

                if ( array_key_exists(1, $rowCount) && $rowCount[ 1 ] != 0 && $rowCount[ 1 ] > $rowCount[ 0 ] ) {
                    $limit .= ', ' . $rowCount[ 1 ];
                }
            }
        }

        if ( $limit ) {
            $bindSql = $boundSql->getSql() . ' LIMIT ' . $limit;
            $boundSql = new SqlBound($bindSql, $boundSql->getBinding());
        }

        return $boundSql;
    }

    /**
     * @param array $dataList
     * @param int $case CASE_LOWER | CASE_UPPER
     * @return array
     */
    private static function _sqlResultPrepare( array $dataList, $case )
    {
        $_dataList = [];

        foreach ( $dataList as $key => $value ) {
            if ( $case == CASE_UPPER ) {
                $_dataList[ strtoupper($key) ] = $value;
            } else {
                $_dataList[ strtolower($key) ] = $value;
            }

        }

        return $_dataList;
    }

}
