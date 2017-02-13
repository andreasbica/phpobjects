<?php

namespace PhpObjects\DataSource\Sql\Statement\Builder;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;
use PhpObjects\DataSource\Sql\Connector\MySqlConnector;
use PhpObjects\DataSource\Sql\Statement\WhereClauseStatement;

class DeleteStatementBuilder
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
     * @param WhereClauseStatement|null $whereClauseBind
     * @return SqlDataBind
     * @throws \Exception
     */
    public function build( $schema, $table, WhereClauseStatement $whereClauseBind = null )
    {
        if ( $this->_connectorType == MySqlConnector::TYPE ) {
            return $this->_buildMySqlStatement($schema, $table, $whereClauseBind);
        }

        throw new \Exception('Connector statement builder not found [' . $this->_connectorType . '].');
    }

    /**
     * @param string $schema
     * @param string $table
     * @param WhereClauseStatement $whereClauseBind
     * @return SqlDataBind
     */
    private function _buildMySqlStatement( $schema, $table, WhereClauseStatement $whereClauseBind )
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

}
