<?php

namespace PhpObjects\DataSource\Sql\Connector;

use PhpObjects\DataSource\Sql\Statement\DataStatement;
use PhpObjects\DataSource\Sql\Statement\ColumnStatement;
use PhpObjects\DataSource\Sql\Statement\OrderClauseStatement;
use PhpObjects\DataSource\Sql\Statement\WhereClauseStatement;
use PhpObjects\DataSource\Sql\ConnectionId\ConnectionIdInterface;
use PhpObjects\DataSource\Sql\Bind\SqlDataBind;

interface ConnectorInterface
{

    /**
     * @return ConnectionIdInterface
     */
    public function getConnectionId();

    /**
     * @return bool
     */
    public function openConnection();

    public function destroyConnection();

    /**
     * @return \mysqli
     */
    public function getConnection();

    /**
     * @param string $sql
     * @param array $dataBindList
     * @param bool $onlyFirstRow
     * @param int $case
     * @return array | bool
     * @throws \Exception Any inner exceptions will be caught and rethrown
     */
    public function execute( $sql, array $dataBindList = array(), $onlyFirstRow = false, $case = null );

    /**
     * @param SqlDataBind $boundSql SQL an binding to be executed
     * @param bool $onlyFirstRow wether or not only the first result shall be returned
     * @param int $case (CASE_UPPER|CASE_LOWER)
     * @return array
     * @throws \Exception
     */
    public function executeWithSqlDataBind( SqlDataBind $boundSql, $onlyFirstRow = false, $case = CASE_LOWER );

    /**
     * @param string $schema
     * @param string $table
     * @param ColumnStatement $columnBind
     * @param WhereClauseStatement $whereClauseBind
     * @param OrderClauseStatement $orderClauseBind
     * @param array $rowCount
     * @return SqlDataBind
     */
    public function buildSelectStatement(
        $schema,
        $table,
        ColumnStatement $columnBind = null,
        WhereClauseStatement $whereClauseBind = null,
        OrderClauseStatement $orderClauseBind = null,
        array $rowCount = [0, 0]
    );

    /**
     * @param string $schema
     * @param string $table
     * @param DataStatement $dataBind
     * @return SqlDataBind
     */
    public function buildInsertStatement( $schema, $table, DataStatement $dataBind );

    /**
     * @param string $schema
     * @param string $table
     * @param DataStatement $dataBind
     * @param WhereClauseStatement $whereClauseBind
     * @return SqlDataBind
     */
    public function buildUpdateStatement( $schema, $table, DataStatement $dataBind, WhereClauseStatement $whereClauseBind = null );

    /**
     * @param string $schema
     * @param string $table
     * @param WhereClauseStatement $whereClauseBind
     * @return SqlDataBind
     */
    public function buildDeleteStatement( $schema, $table, WhereClauseStatement $whereClauseBind = null );

}
