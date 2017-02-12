<?php

namespace PhpObjects\Core;

class UuidV4
{

    /**
     * @var string
     */
    private $_uuid = '';


    public function __construct()
    {
        $this->_uuid = $this->_generate();
    }

    private function _generate()
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[ 6 ] = chr(ord($data[ 6 ]) & 0x0f | 0x40);
        $data[ 8 ] = chr(ord($data[ 8 ]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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
