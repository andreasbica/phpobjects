<?php

namespace PhpObjects\DataSource\Bind;

use PhpObjects\DataSource\SqlBound;

class WhereClause
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
     * @return WhereClause
     */
    public static function create()
    {
        return new self();
    }


    public static function createFromArray( array $whereClauseList )
    {
        $self = self::create();

        foreach ( $whereClauseList as $key => $clauseList ) {
            switch (strtoupper($key)) {
                case self::WHERE_OR:
                    self::_createFromArray($self, $clauseList, 'addOr');
                    break;

                case self::WHERE_LIKE:
                    self::_createFromArray($self, $clauseList, 'addLike');
                    break;

                case self::WHERE_ORLIKE:
                    self::_createFromArray($self, $clauseList, 'addOrLike');
                    break;

                case self::WHERE_NOTLIKE:
                case '?NOTLIKE':
                    self::_createFromArray($self, $clauseList, 'addNotLike');
                    break;

                case self::WHERE_ORNOTLIKE:
                case '?ORNOTLIKE':
                    self::_createFromArray($self, $clauseList, 'addOrNotLike');
                    break;

                default:
                    $_clauseList = [];
                    $_clauseList[ $key ] = $clauseList;

                    self::_createFromArray($self, $_clauseList, 'add');
                    break;
            }
        }

        return $self;
    }


    public function add( $field, $value )
    {
        $this->_whereList[ $field ][] = $value;
        return $this;
    }


    public function addOr( $field, $value )
    {
        $this->_whereList[ self::WHERE_OR ][ $field ][] = $value;
        return $this;
    }


    public function addLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_LIKE ][ $field ][] = $value;
        return $this;
    }


    public function addNotLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_NOTLIKE ][ $field ][] = $value;
        return $this;
    }


    public function addOrLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_ORLIKE ][ $field ][] = $value;
        return $this;
    }


    public function addOrNotLike( $field, $value )
    {
        $this->_whereList[ self::WHERE_ORNOTLIKE ][ $field ][] = $value;
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
     * @return SqlBound
     */
    public function getSqlBound()
    {
        return self::_buildWhereWithBinding($this->getBinding());
    }


    /**
     * @return string
     */
    public function getSqlResolved()
    {
        return $this->getSqlBound()->getSql(true);
    }


    /**
     * @param  array $whereList
     * @return SqlBound
     */
    private static function _buildWhereWithBinding( array $whereList )
    {
        $binding = $whereElements = [];
        $whereElements = self::_getWhereStatement($binding, $whereList);
        $sql = '';

        if ( $whereElements ) {
            $sql = ' WHERE ' . implode(' AND ', $whereElements);
        }

        return SqlBound::create($sql, $binding);
    }


    /**
     * @param array $binding
     * @param array $whereList
     * @param string $clauseOperator
     * @return array
     */
    private static function _getWhereStatement( array &$binding, array $whereList, $clauseOperator = ' = ' )
    {
        $whereElements = [];

        foreach ( $whereList as $key => $val ) {
            switch (strtoupper($key)) {
                case self::WHERE_OR:
                    $whereElements[] = '(' . implode(' OR ', self::_getWhereStatement($binding, $val)) . ')';
                    break;

                case self::WHERE_LIKE:
                    $whereElements[] = implode(' AND ', self::_getWhereStatement($binding, $val, ' LIKE '));
                    break;

                case self::WHERE_ORLIKE:
                    $whereElements[] = '(' . implode(' OR ', self::_getWhereStatement($binding, $val, ' LIKE ')) . ')';
                    break;

                case self::WHERE_NOTLIKE:
                case '?NOTLIKE':
                    $whereElements[] = implode(' AND ', self::_getWhereStatement($binding, $val, ' NOT LIKE '));
                    break;

                case self::WHERE_ORNOTLIKE:
                case '?ORNOTLIKE':
                    $whereElements[] = '(' . implode(' OR ', self::_getWhereStatement($binding, $val, ' NOT LIKE ')) . ')';
                    break;

                default:

                    if ( !is_array($val) ) {
                        $val = [$val];
                    }

                    foreach ( $val as $valIdx => $valItem ) {
                        if ( $valItem === null || strtolower($valItem) === 'null' ) {
                            $whereElements[] = strtolower($key) . ' IS NULL';
                        } else if ( strtolower($valItem) == 'not null' || strtolower($valItem) == '!null' ) {
                            $whereElements[] = strtolower($key) . ' IS NOT NULL';
                        } else {
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


    private static function _createFromArray( $self, array $clauseList, $addFunction )
    {
        foreach ( $clauseList as $field => $values ) {
            if ( !is_array($values) ) {
                $values = [$values];
            }

            foreach ( $values as $value ) {
                $self->$addFunction($field, $value);
            }
        }
    }

}
