<?php

namespace PhpObjects\Formatter;

class ToJson implements ToJsonInterface
{
    protected $_bitMask = 0;


    /**
     * @param int $bitMask
     */
    protected function __construct( $bitMask )
    {
        $this->_bitMask = $bitMask;
    }


    /**
     * @return ToJson
     */
    public static function STANDARD()
    {
        return new self(0);
    }


    /**
     * @return ToJson
     */
    public static function HEX_TAG()
    {
        return new self(JSON_HEX_TAG);
    }


    /**
     * @return ToJson
     */
    public static function HEX_APOS()
    {
        return new self(JSON_HEX_APOS);
    }


    /**
     * @return ToJson
     */
    public static function HEX_QUOT()
    {
        return new self(JSON_HEX_QUOT);
    }


    /**
     * @return ToJson
     */
    public static function HEX_AMP()
    {
        return new self(JSON_HEX_AMP);
    }


    /**
     * @return ToJson
     */
    public static function FORCE_OBJECT()
    {
        return new self(JSON_FORCE_OBJECT);
    }


    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @return string
     */
    public function cast( $data, ToCase $keyToCase = null )
    {
        $data = (new ToArray)->cast($data, $keyToCase, ToEncodingMapper::UTF_8());
        return json_encode($data, $this->_bitMask);
    }

}
