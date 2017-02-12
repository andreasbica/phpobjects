<?php

namespace PhpObjects\DataSource\Sql\Statement;

class ColumnStatement
{

    private $_columnList = [];


    public function __construct()
    {
    }

    /**
     * @param array $columnList
     * @return $this
     */
    public function setBinding( array $columnList )
    {
        foreach ( $columnList as $field ) {
            $this->addColumn($field);
        }
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function addColumn( $field )
    {
        $this->_columnList[] = $field;
        return $this;
    }


    /**
     * @return array
     */
    public function getBinding()
    {
        return $this->_columnList;
    }


    /**
     * @return string
     */
    public function getResolvedSql()
    {
        return implode(', ', $this->getBinding());
    }

}
