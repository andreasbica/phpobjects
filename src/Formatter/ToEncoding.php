<?php

namespace PhpObjects\Formatter;

use PhpObjects\Core\Encoding;

class ToEncodingMapper implements ToEncodingMapperInterface
{
    protected $_toEncoding;


    /**
     * @param string $toEncoding
     */
    public function __construct( $toEncoding )
    {
        $this->_toEncoding = $toEncoding;
    }


    /**
     * Encode to UTF-8.
     * @return ToCase
     */
    public static function UTF_8()
    {
        return new self('UTF-8');
    }


    /**
     * Encode to ISO-8859-1.
     * @return ToCase
     */
    public static function ISO_8859_1()
    {
        return new self('ISO-8859-1');
    }


    /**
     * @param  string $string
     * @return string
     */
    public function cast( $string )
    {
        $_string = Encoding::encode($this->_toEncoding, $string);
        return $_string;
    }

}
