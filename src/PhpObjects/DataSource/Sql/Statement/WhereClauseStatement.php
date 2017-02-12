<?php

namespace PhpObjects\DataSource\Sql\Statement;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;

class WhereClauseStatement
{

    const WHERE_OR = '?OR';
    const WHERE_LIKE = '?LIKE';
    const WHERE_NOTLIKE = '?!LIKE';
    const WHERE_ORLIKE = '?ORLIKE';
    const WHERE_ORNOTLIKE = '?OR!LIKE';

    private $_whereList = [];


    public function __construct()
    {
    }

    /**
     * @param array $whereClauseList
     * @return mixed
     */
    public function setBinding( array $whereClauseList )
    {
        foreach ( $whereClauseList as $key => $clauseList ) {
            switch (strtoupper($key)) {
                case self::WHERE_OR:
                    $this->_createFromArray($clauseList, 'addOr');
                    break;

                case self::WHERE_LIKE:
                    $this->_createFromArray($clauseList, 'addLike');
                    break;

                case self::WHERE_ORLIKE:
                    $this->_createFromArray($clauseList, 'addOrLike');
                    break;

                case self::WHERE_NOTLIKE:
                    $this->_createFromArray($clauseList, 'addNotLike');
                    break;

                case self::WHERE_ORNOTLIKE:
                    $this->_createFromArray($clauseList, 'addOrNotLike');
                    break;

                default:
                    $_clauseList = [];
                    $_clauseList[ $key ] = $clauseList;

                    $this->_createFromArray($_clauseList, 'add');
                    break;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBinding()
    {
        return $this->_whereList;
    }

    /**
     * @param array $clauseList
     * @param string $addFunction
     */
    private function _createFromArray( array $clauseList, $addFunction )
    {
        foreach ( $clauseList as $field => $values ) {
            if ( !is_array($values) ) {
                $values = [$values];
            }

            foreach ( $values as $value ) {
                $this->$addFunction($field, $value);
            }
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function add( $field, $value )
    {
        $this->_whereList[ $field ][] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addOr( $field, $value )
    {
        $this->_whereList[ self::WHERE_OR ][ $field ][] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_LIKE ][ $field ][] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addNotLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_NOTLIKE ][ $field ][] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addOrLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_ORLIKE ][ $field ][] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addOrNotLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_ORNOTLIKE ][ $field ][] = $value;
        return $this;
    }

    /**
     * @return SqlDataBind
     */
    public function getSqlDataBind()
    {
        return $this->_buildWhereWithBinding($this->getBinding());
    }

    /**
     * @return string
     */
    public function getResolvedSql()
    {
        return $this->getSqlDataBind()->getSql(true);
    }

    /**
     * @param  array $whereList
     * @return SqlDataBind
     */
    private function _buildWhereWithBinding( array $whereList )
    {
        $binding = $whereElements = [];
        $whereElements = $this->_getWhereStatement($binding, $whereList);
        $sql = '';

        if ( $whereElements ) {
            $sql = ' WHERE ' . implode(' AND ', $whereElements);
        }

        return new SqlDataBind($sql, $binding);
    }

    /**
     * @param array $binding
     * @param array $whereList
     * @param string $clauseOperator
     * @return array
     */
    private function _getWhereStatement( array &$binding, array $whereList, $clauseOperator = ' = ' )
    {
        $whereElements = [];

        foreach ( $whereList as $key => $val )
        {
            switch (strtoupper($key))
            {
                case self::WHERE_OR:
                    $whereElements[] = '(' . implode(' OR ', $this->_getWhereStatement($binding, $val)) . ')';
                    break;

                case self::WHERE_LIKE:
                    $whereElements[] = implode(' AND ', $this->_getWhereStatement($binding, $val, ' LIKE '));
                    break;

                case self::WHERE_ORLIKE:
                    $whereElements[] = '(' . implode(' OR ', $this->_getWhereStatement($binding, $val, ' LIKE ')) . ')';
                    break;

                case self::WHERE_NOTLIKE:
                    $whereElements[] = implode(' AND ', $this->_getWhereStatement($binding, $val, ' NOT LIKE '));
                    break;

                case self::WHERE_ORNOTLIKE:
                    $whereElements[] = '(' . implode(' OR ', $this->_getWhereStatement($binding, $val, ' NOT LIKE ')) . ')';
                    break;

                default:

                    if ( !is_array($val) ) {
                        $val = [$val];
                    }

                    foreach ( $val as $valIdx => $valItem )
                    {
                        if ( $valItem === null || strtolower($valItem) === 'null' ) {
                            $whereElements[] = strtolower($key) . ' IS NULL';
                        }
                        else if ( strtolower($valItem) == 'not null' || strtolower($valItem) == '!null' ) {
                            $whereElements[] = strtolower($key) . ' IS NOT NULL';
                        }
                        else {
                            $bindKey = ':' . strtolower($key) . ($valIdx != 0 ? $valIdx : '');
                            $binding[ $bindKey ] = $valItem;
                            $whereElements[] = strtolower($key) . $clauseOperator . $bindKey;
                        }
                    }
                    break;
            }
        }

        return $whereElements;
    }

}
