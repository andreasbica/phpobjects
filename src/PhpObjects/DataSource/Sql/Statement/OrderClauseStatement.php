<?php

namespace PhpObjects\DataSource\Sql\Statement;

use PhpObjects\DataSource\Sql\Bind\SqlDataBind;

class OrderClauseStatement
{

    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';


    private $_orderList = [];


    public function __construct() {}

    /**
     * @param array $orderClauseList
     * @return $this
     */
    public function setBinding( array $orderClauseList )
    {
        foreach ( $orderClauseList as $field => $orderMode ) {
            if ( strtoupper($orderMode) == self::ORDER_DESC ) {
                $this->addDesc($field);
            }
            else if ( strtoupper($orderMode) == self::ORDER_ASC ) {
                $this->addAsc($field);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBinding()
    {
        return $this->_orderList;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function addAsc( $field )
    {
        $this->_orderList[ $field ] = self::ORDER_ASC;
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function addDesc( $field )
    {
        $this->_orderList[ $field ] = self::ORDER_DESC;
        return $this;
    }

    /**
     * @return SqlDataBind
     */
    public function getSqlDataBind()
    {
        return $this->_buildOrderBinding($this->getBinding());
    }

    /**
     * @return string
     */
    public function getResolvedSql()
    {
        return $this->getSqlDataBind()->getSql(true);
    }

    /**
     * @param  array $orderList
     * @return SqlDataBind
     */
    private function _buildOrderBinding(array $orderList)
    {
        $sql = '';
        
        if ($orderList)
        {
            $orderElements = [];
            
            foreach ($orderList as $field => $direction) {
                $orderElements[] = $field . ' ' . $direction; // (strtoupper($direction) === self::ORDER_DESC ? self::ORDER_DESC : self::ORDER_ASC);
            }

            $sql = ' ORDER BY ' . implode(', ', $orderElements);
        }

        return new SqlDataBind($sql, []);
    }

}
