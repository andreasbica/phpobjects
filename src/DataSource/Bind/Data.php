<?php

namespace PhpObjects\DataSource\Bind;

use PhpObjects\DataSource\SqlBound;

class Data
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
     * @return $this
     */
    public static function create()
    {
        return new self();
    }


    public static function createFromArray( array $dataList )
    {
        $self = self::create();

        foreach ( $dataList as $field => $value ) {
            $self->addData($field, $value);
        }

        return $self;
    }


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
     * @return SqlBound
     */
    public function getSqlBound( $sqlOperation = '' )
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

        return SqlBound::create($sql, $binding);
    }


    /**
     * @param  string $sqlOperation One of SQL_OPERATION_*.
     * @return string
     */
    public function getSqlResolved( $sqlOperation = '' )
    {
        return $this->getSqlBound($sqlOperation)->getSql(true);
    }


    private function _tryCastBindToCommandBind( $key, array &$castValueList, $bindKey )
    {
        if ( $castValueList && array_key_exists($key, $castValueList) ) {
            $bindKey = sprintf($castValueList[ $key ], addslashes($bindKey));
        }

        return $bindKey;
    }

}
