<?php

namespace PhpObjects\DataSource\Sql\Connector;

use PhpObjects\DataSource\Sql\Statement\DataStatement;
use PhpObjects\DataSource\Sql\Statement\ColumnStatement;
use PhpObjects\DataSource\Sql\Statement\OrderClauseStatement;
use PhpObjects\DataSource\Sql\Statement\WhereClauseStatement;
use PhpObjects\DataSource\Sql\ConnectionId\ConnectionIdInterface;
use PhpObjects\DataSource\Sql\ConnectionId\MySqlConnectionId;
use PhpObjects\DataSource\Sql\ConnectionId\MySqlConnectionIdInterface;
use PhpObjects\DataSource\Sql\Bind\SqlDataBind;

class MySqlConnector implements ConnectorInterface
{

    const TYPE = MySqlConnectionIdInterface::TYPE;


    /**
     * @var \mysqli
     */
    private $_connection = null;

    /**
     * @var MySqlConnectionIdInterface
     */
    private $_connectionId = null;


    /**
     * @param ConnectionIdInterface $mySqlConnectionId
     */
    public function __construct( MySqlConnectionIdInterface $mySqlConnectionId )
    {
        $this->_connectionId = $mySqlConnectionId;
    }

    /**
     * @return MySqlConnectionIdInterface
     */
    public function getConnectionId()
    {
        return $this->_connectionId;
    }

    /**
     * @inheritdoc
     */
    public function openConnection()
    {
        $this->_connection = new \mysqli(
            $this->_connectionId->getKey( MySqlConnectionId::KEY_HOST ),
            $this->_connectionId->getKey( MySqlConnectionId::KEY_USER ),
            $this->_connectionId->getKey( MySqlConnectionId::KEY_PASSWORD ),
            $this->_connectionId->getKey( MySqlConnectionId::KEY_DATABASE ),
            $this->_connectionId->getKey( MySqlConnectionId::KEY_PORT )
        );

        if ( $this->_connection->connect_error ) {
            $this->_connection = null;
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroyConnection()
    {
        if ( $this->_connection ) {
            $this->_connection->close();
        }

        $this->_connectionId = null;
        $this->_connection = null;
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @inheritdoc
     */
    public function execute( $sql, array $dataBindList = array(), $onlyFirstRow = false, $case = null )
    {
        if ( $dataBindList ) {
            $sql = (new SqlDataBind($sql, $dataBindList))->getSql(true);
        }

        if ( !$this->getConnection() ) {
            throw new \Exception('Connection to mysql failed.');
        }

        $sqlResult = $this->getConnection()->query($sql, MYSQLI_USE_RESULT);

        if ( is_object($sqlResult) ) {
            if ( $onlyFirstRow ) {
                $dataList = $sqlResult->fetch_assoc();
            } else {
                $dataList = $sqlResult->fetch_all(MYSQLI_ASSOC);
            }

            if ( $dataList && ($case === CASE_UPPER || $case === CASE_LOWER) ) {
                $dataList = $this->_sqlResultPrepare($dataList, $case);
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
     * @inheritdoc
     */
    final public function executeWithSqlDataBind( SqlDataBind $boundSql, $onlyFirstRow = false, $case = CASE_LOWER )
    {
        $sql = $boundSql->getSql(true);
        return $this->execute($sql, [], $onlyFirstRow, $case);
    }

    /**
     * @inheritdoc
     */
    public function buildSelectStatement(
        $schema,
        $table,
        ColumnStatement $columnBind = null,
        WhereClauseStatement $whereClauseBind = null,
        OrderClauseStatement $orderClauseBind = null,
        array $rowCount = [0, 0]
    ) {
        $fieldNameList = ($columnBind ? $columnBind->getBinding() : []);
        $fieldlistString = (empty($fieldNameList)) ? '*' : implode(', ', $fieldNameList);
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);

        $sql = "SELECT $fieldlistString FROM $tableString";
        $binding = [];

        if ( ($whereSql = ($whereClauseBind ? $whereClauseBind->getSqlDataBind()->getSql() : '')) ) {
            $binding = $whereClauseBind->getSqlDataBind()->getBinding();
            $sql .= $whereSql;
        }

        if ( ($orderSql = ($orderClauseBind ? $orderClauseBind->getResolvedSql() : '')) ) {
            $sql .= $orderSql;
        }

        $boundSql = new SqlDataBind($sql, $binding);
        $boundSql = $this->_appendRownumClause($boundSql, $rowCount);

        return $boundSql;
    }

    /**
     * @inheritdoc
     */
    public function buildInsertStatement( $schema, $table, DataStatement $dataBind )
    {
        $keys = $insertSql = '';
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $dataSqlBound = $dataBind->getSqlDataBind(DataStatement::SQL_OPERATION_INSERT);
        $binding = $dataSqlBound->getBinding();

        if ( $dataSqlBound ) {
            $keys = implode(', ', array_keys($dataBind->getBinding()));
            $insertSql = $dataSqlBound->getSql();
        }

        $sql = "INSERT INTO $tableString (" . $keys . ') VALUES (' . $insertSql . ')';

        return new SqlDataBind($sql, $binding);
    }

    /**
     * @inheritdoc
     */
    public function buildUpdateStatement( $schema, $table, DataStatement $dataBind, WhereClauseStatement $whereClauseBind = null )
    {
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $dataSqlBound = $dataBind->getSqlDataBind(DataStatement::SQL_OPERATION_UPDATE);

        $sql = "UPDATE $tableString SET ";
        $sql .= $dataSqlBound->getSql();

        $binding = $dataSqlBound->getBinding();

        if ( $whereClauseBind ) {
            $sql .= $whereClauseBind->getSqlDataBind()->getSql();
            $binding = array_merge($binding, $whereClauseBind->getSqlDataBind()->getBinding());
        }

        return new SqlDataBind($sql, $binding);
    }

    /**
     * @inheritdoc
     */
    public function buildDeleteStatement( $schema, $table, WhereClauseStatement $whereClauseBind = null )
    {
        $tableString = ($schema ? strtolower($schema) . '.' . $table : $table);
        $sql = "DELETE FROM $tableString";
        $binding = [];

        if ( $whereClauseBind ) {
            $sql .= $whereClauseBind->getSqlDataBind()->getSql();
            $binding = $whereClauseBind->getSqlDataBind()->getBinding();
        }

        return new SqlDataBind($sql, $binding);
    }

    /**
     * @param  SqlDataBind $boundSql
     * @param  array $rowCount
     * @return SqlDataBind
     */
    private function _appendRownumClause( SqlDataBind $boundSql, array $rowCount )
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
            $boundSql = new SqlDataBind($bindSql, $boundSql->getBinding());
        }

        return $boundSql;
    }

    /**
     * @return int
     */
    public function getNextSequenceValue()
    {
        return (mysqli_insert_id($this->_connection) + 1);
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        return mysqli_insert_id($this->_connection);
    }

    /**
     * @param  string $table
     * @param  int $case CASE_UPPER | CASE_LOWER
     * @return array
     */
    public function getDescribeTable( $table, $case = CASE_LOWER )
    {
        $result = $this->execute('describe ' . $table, [], false, $case);

        if ( $result === false ) {
            return [];
        }

        return $result;
    }

    /**
     * @param array $dataList
     * @param int $case CASE_LOWER | CASE_UPPER
     * @return array
     */
    private function _sqlResultPrepare( array $dataList, $case )
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
