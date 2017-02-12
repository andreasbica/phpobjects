<?php

namespace PhpObjects\DataSource\Sql\Statement;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;

class DataStatement
{

    const CAST_VALUE = '!CASTVALUE';

    const SQL_OPERATION_INSERT = 'insert';
    const SQL_OPERATION_UPDATE = 'update';


    private $_dataList = [];
    private $_castList = [];


    public function __construct()
    {
    }

    /**
     * @param array $dataList
     * @return $this
     */
    public function setBinding( array $dataList )
    {
        foreach ( $dataList as $field => $value ) {
            $this->addData($field, $value);
        }
        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function addData( $field, $value )
    {
        $this->_dataList[ $field ] = $value;
        return $this;
    }


    /**
     * @param  string $field
     * @param  string $command
     * @return $this
     */
    public function addCastValue( $field, $command )
    {
        $this->_castList[ self::CAST_VALUE ][ $field ] = $command;
        return $this;
    }


    /**
     * @return array
     */
    public function getBinding()
    {
        return $this->_dataList;
    }


    /**
     * @param  string $sqlOperation
     * @return SqlDataBind
     */
    public function getSqlDataBind( $sqlOperation = '' )
    {
        $sql = '';
        $castValueList = $sqlItemList = $binding = [];

        if ( $this->_castList && array_key_exists(self::CAST_VALUE, $this->_castList) ) {
            $castValueList = &$this->_castList[ self::CAST_VALUE ];
        }

        foreach ( $this->getBinding() as $key => $value ) {
            $bindKey = ":" . $key = strtolower($key);

            if ( $sqlOperation == self::SQL_OPERATION_INSERT ) {
                $sqlItemList[] = $this->_tryCastBindToCommandBind($key, $castValueList, $bindKey);
            } else if ( $sqlOperation == self::SQL_OPERATION_UPDATE ) {
                $bindKey = ":val_" . $key;
                $sqlItemList[] = $key . ' = ' . $this->_tryCastBindToCommandBind($key, $castValueList, $bindKey);
            }

            $binding[ $bindKey ] = $value;
        }

        if ( $sqlItemList ) {
            $sql = implode(', ', $sqlItemList);
        }

        return new SqlDataBind($sql, $binding);
    }


    /**
     * @param  string $sqlOperation One of SQL_OPERATION_*.
     * @return string
     */
    public function getResolvedSql( $sqlOperation = '' )
    {
        return $this->getSqlDataBind($sqlOperation)->getSql(true);
    }


    private function _tryCastBindToCommandBind( $key, array &$castValueList, $bindKey )
    {
        if ( $castValueList && array_key_exists($key, $castValueList) ) {
            $bindKey = sprintf($castValueList[ $key ], addslashes($bindKey));
        }

        return $bindKey;
    }

}
