<?php

namespace PhpObjects\TypeCast\Strategy\Type\ToArray;

class FromGenericString implements FromFormatTypeInterface
{

    const TYPE = 'from:string:generic';

    private $_elementDelimiter = ',';
    private $_keyValueDelimiter = '=';
    private $_trim = '';


    public function __construct()
    {}

    /**
     * @return string
     */
    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setKeyValueDelimiter( $delimiter )
    {
        $this->_keyValueDelimiter = (string) $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeyValueDelimiter()
    {
        return $this->_keyValueDelimiter;
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

    /**
     * @param string $charSet
     * @return $this
     */
    public function setTrimCharSet( $charSet )
    {
        $this->_trim = (string) $charSet;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrimCharSet()
    {
        return $this->_trim;
    }

}
