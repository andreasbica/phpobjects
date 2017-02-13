<?php

namespace PhpObjects\DataSource\Sql\Statement\Builder;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;
use PhpObjects\DataSource\Sql\Connector\MySqlConnector;
use PhpObjects\DataSource\Sql\Statement\DataStatement;
use PhpObjects\DataSource\Sql\Statement\WhereClauseStatement;

class UpdateStatementBuilder
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
     * @param DataStatement $dataBind
     * @param WhereClauseStatement|null $whereClauseBind
     * @return SqlDataBind
     * @throws \Exception
     */
    public function build( $schema, $table, DataStatement $dataBind, WhereClauseStatement $whereClauseBind = null )
    {
        if ( $this->_connectorType == MySqlConnector::TYPE ) {
            return $this->_buildMySqlStatement($schema, $table, $dataBind, $whereClauseBind);
        }

        throw new \Exception('Connector statement builder not found [' . $this->_connectorType . '].');
    }

    /**
     * @param string $schema
     * @param string $table
     * @param DataStatement $dataBind
     * @param WhereClauseStatement $whereClauseBind
     * @return SqlDataBind
     */
    private function _buildMySqlStatement( $schema, $table, DataStatement $dataBind, WhereClauseStatement $whereClauseBind )
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

}
