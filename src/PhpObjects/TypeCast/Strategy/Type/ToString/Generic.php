<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToString;

class Generic implements ToStringTypeInterface
{

    const TYPE = 'to:generic';

    private $_elementDelimiter = ',';
    private $_keyFormat = '%s=';
    private $_valueFormat = '%s';

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setKeyFormat( $format )
    {
        $this->_keyFormat = (string) $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeyFormat()
    {
        return $this->_keyFormat;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setValueFormat( $format )
    {
        $this->_valueFormat = (string) $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getValueFormat()
    {
        return $this->_valueFormat;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setElementDelimiter( $delimiter )
    {
        $this->_elementDelimiter = (string) $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementDelimiter()
    {
        return $this->_elementDelimiter;
    }

}
