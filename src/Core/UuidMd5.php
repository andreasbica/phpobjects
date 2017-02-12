<?php

namespace PhpObjects\Core;

class UuidMd5
{

    /**
     * @var string
     */
    private $_uuid = '';


    /**
     * @param int $length
     */
    public function __construct( $length = 16 )
    {
        $this->_uuid = $this->_generate($length);
    }

    private function _generate( $length )
    {
        srand((double)microtime() * 1000000);
        $guid = md5(uniqid(microtime() + rand(100000, 999999)));

        return substr($guid, 0, $length);
    }

    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_uuid;
    }

}
