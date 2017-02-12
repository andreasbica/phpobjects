<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\Core\Encoding;
use PhpObjects\TypeCast\Strategy\Mapper\MapperTrait\KeyAndValueTrait;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToEncoding\ToEncodingMapperTypeInterface;

class ToEncodingMapper implements ToEncodingMapperInterface
{

    const TYPE = 'mapper:to:encoding';

    /**
     * @var ToEncodingMapperTypeInterface
     */
    private $_toEncoding;


    public function __construct()
    {
    }

    use KeyAndValueTrait;
    

    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function setEncoding( ToEncodingMapperTypeInterface $encoding )
    {
        $this->_toEncoding = $encoding;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cast( $string )
    {
        $string = (string) $string;

        $items = explode(':', (string) $this->_toEncoding);
        $toEncoding = $items[ (count($items) - 1) ];

        if ($this->_toEncoding) {
            return Encoding::encode($toEncoding, $string);
        }

        return $string;
    }

}
