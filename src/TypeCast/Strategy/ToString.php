<?php

namespace PhpObjects\TypeCast\Strategy;

use PhpObjects\TypeCast\Strategy\StrategyTrait\MapperTrait;
use PhpObjects\TypeCast\Strategy\Type\ToString\Generic;
use PhpObjects\TypeCast\Strategy\Type\ToString\Json;
use PhpObjects\TypeCast\Strategy\Type\ToString\Serialize;
use PhpObjects\TypeCast\Strategy\Type\ToString\ToStringTypeInterface;
use PhpObjects\TypeCast\Strategy\Type\ToString\Unserialize;

class ToString implements ToStringInterface
{

    const TYPE = 'to:string';

    /**
     * @var ToStringTypeInterface
     */
    private $_stringType;


    public function __construct()
    {}

    use MapperTrait;


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function setFormat( ToStringTypeInterface $stringType )
    {
        $this->_stringType = $stringType;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cast( $data )
    {
        $stringType = $this->_stringType;

        switch ((string) $stringType)
        {
            case Json::TYPE:
                /* @var $stringType Json */
                return json_encode($data, $stringType->getBitMask());

            case Serialize::TYPE:
                return serialize($data);

            case Unserialize::TYPE:
                return unserialize($data);

            case Generic::TYPE:
                (is_array($data) ?: $data = (array) $data);
                /* @var $stringType Generic */
                return $this->_castToGenericFormat($stringType, $data);

            default:
                if ( is_object($data) || is_array($data) ) {
                    return (string) $data;
                }
                return $data;
        }
    }

    /**
     * @param Generic $stringType
     * @param array $dataList
     * @return string
     */
    private function _castToGenericFormat( Generic $stringType, array $dataList )
    {
        $formatedList = [];

        foreach ( $dataList as $key => $value ) {

            $keyValueString = '';
            
            if ($stringType->getKeyFormat()) {
                $keyValueString .= sprintf($stringType->getKeyFormat(), $key);
            }

            if ($stringType->getValueFormat()) {
                $keyValueString .= sprintf($stringType->getValueFormat(), $value);
            }

            $formatedList[] = $keyValueString;
        }
        
        return implode($stringType->getElementDelimiter(), $formatedList);
    }

}
