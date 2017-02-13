<?php

namespace PhpObjects\DataSource\Sql\Statement\Builder;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;
use PhpObjects\DataSource\Sql\Connector\MySqlConnector;
use PhpObjects\DataSource\Sql\Statement\ColumnStatement;
use PhpObjects\DataSource\Sql\Statement\OrderClauseStatement;
use PhpObjects\DataSource\Sql\Statement\WhereClauseStatement;

class SelectStatementBuilder
{

    /**
     * @var string
     */
    private $_connectorType;


    public function __construct( $connectorType )
    {
        $this->_connectorType = $connectorType;
    }

    /**
     * @param string $schema
     * @param string $table
     * @param ColumnStatement|null $columnBind
     * @param WhereClauseStatement|null $whereClauseBind
     * @param OrderClauseStatement|null $orderClauseBind
     * @param array $rowCount
     * @return SqlDataBind
     * @throws \Exception
     */
    public function build(
        $schema,
        $table,
        ColumnStatement $columnBind = null,
        WhereClauseStatement $whereClauseBind = null,
        OrderClauseStatement $orderClauseBind = null,
        array $rowCount = [0, 0]
    ) {
        if ( $this->_connectorType == MySqlConnector::TYPE ) {
            return $this->_buildMySqlStatement($schema, $table, $columnBind, $whereClauseBind, $orderClauseBind, $rowCount);
        }

        throw new \Exception('Connector statement builder not found [' . $this->_connectorType . '].');
    }

    /**
     * @param string $schema
     * @param string $table
     * @param ColumnStatement $columnBind
     * @param WhereClauseStatement $whereClauseBind
     * @param OrderClauseStatement $orderClauseBind
     * @param array $rowCount
     * @return SqlDataBind
     */
    private function _buildMySqlStatement( $schema, $table, ColumnStatement $columnBind, WhereClauseStatement $whereClauseBind, OrderClauseStatement $orderClauseBind, array $rowCount )
    {
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

        return $this->_buildMySqlStatementAppendRownumClause((new SqlDataBind($sql, $binding)), $rowCount);
    }

    /**
     * @param  SqlDataBind $boundSql
     * @param  array $rowCount
     * @return SqlDataBind
     */
    private function _buildMySqlStatementAppendRownumClause( SqlDataBind $boundSql, array $rowCount )
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

}
