<?php

namespace PhpObjects\DataSource\Bind;

use PhpObjects\DataSource\SqlBound;

class OrderClause
{

    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';


    private $_orderList = [];


    public function __construct() {}


    /**
     * @return OrderClause
     */
    public static function create()
    {
        return new self();
    }
    
    
    public static function createFromArray(array $orderClauseList)
    {
        $self = self::create();
        
        foreach ($orderClauseList as $field => $orderMode)
        {
            if (strtoupper($orderMode) == self::ORDER_DESC) {
                $self->addDesc($field);
            }
            else if (strtoupper($orderMode) == self::ORDER_ASC) {
                $self->addAsc($field);
            }
        }
        
        return $self;
    }


    public function addAsc($field)
    {
        $this->_orderList[$field] = self::ORDER_ASC;
        return $this;
    }


    public function addDesc($field)
    {
        $this->_orderList[$field] = self::ORDER_DESC;
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
     * @return SqlBound
     */
    public function getSqlBound()
    {
        return self::_buildOrderBinding($this->getBinding());
    }
    
    
    /**
     * @return string
     */
    public function getSqlResolved()
    {
        return $this->getSqlBound()->getSql(true);
    }


    /**
     * @param  array $orderList
     * @return SqlBound
     */
    private static function _buildOrderBinding(array $orderList)
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

        return SqlBound::create($sql, []);
    }

}
