<?php

namespace PhpObjects\DataSource\Sql\Statement\Builder;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;
use PhpObjects\DataSource\Sql\Connector\MySqlConnector;
use PhpObjects\DataSource\Sql\Statement\DataStatement;

class InsertStatementBuilder
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
     * @return SqlDataBind
     * @throws \Exception
     */
    public function build( $schema, $table, DataStatement $dataBind )
    {
        if ( $this->_connectorType == MySqlConnector::TYPE ) {
            return $this->_buildMySqlStatement($schema, $table, $dataBind);
        }

        throw new \Exception('Connector statement builder not found [' . $this->_connectorType . '].');
    }

    /**
     * @param string $schema
     * @param string $table
     * @param DataStatement $dataBind
     * @return SqlDataBind
     */
    private function _buildMySqlStatement( $schema, $table, DataStatement $dataBind )
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

}
