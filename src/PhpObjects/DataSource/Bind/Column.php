<?php

namespace PhpObjects\DataSource\Bind;

class Column
{

    private $_columnList = [];


    public function __construct()
    {
    }


    /**
     * @return Column
     */
    public static function create()
    {
        return new self();
    }


    public static function createFromArray( array $columnList )
    {
        $self = self::create();

        foreach ( $columnList as $field ) {
            $self->addColumn($field);
        }

        return $self;
    }


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
    public function getSqlResolved()
    {
        return implode(', ', $this->getBinding());
    }

}
