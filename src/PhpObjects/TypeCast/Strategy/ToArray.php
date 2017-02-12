<?php

namespace PhpObjects\TypeCast\Strategy;

use PhpObjects\TypeCast\Strategy\Mapper\ToCaseMapper;
use PhpObjects\TypeCast\Strategy\Mapper\ToEncodingMapper;
use PhpObjects\TypeCast\Strategy\Mapper\MapperStrategyInterface;
use PhpObjects\TypeCast\Strategy\StrategyTrait\MapperTrait;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromFormatTypeInterface;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromGenericString;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromJson;

class ToArray implements ToArrayInterface
{

    const TYPE = 'to:array';

    /**
     * @var FromFormatTypeInterface
     */
    private $_fromFormatType;


    public function __construct()
    {
        $this->resetMapper();
    }

    use MapperTrait;

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @param FromFormatTypeInterface $formatType
     * @return $this
     */
    public function fromFormat( FromFormatTypeInterface $formatType )
    {
        $this->_fromFormatType = $formatType;
        return $this;
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function cast( $data )
    {
        $this->_tryToAutoDetectTypeFormatFrom($data);
        $formatType = $this->_fromFormatType;

        switch ((string) $formatType)
        {
            case FromJson::TYPE:
                $dataList = json_decode($data, true);
                break;

            case FromGenericString::TYPE:
                /* @var $formatType FromGenericString */
                $dataList = $this->_castFromGenericFormat($formatType, (string) $data);
                break;

            default:
                $dataList = $this->_castToArray($data);
                break;
        }

        $toCaseMapper = $this->filterMapperByType(ToCaseMapper::TYPE, true);
        $toEncodingMapper = $this->filterMapperByType(ToEncodingMapper::TYPE, true);

        return $this->_applyMapper($dataList, $toCaseMapper, $toEncodingMapper);
    }

    /**
     * @param mixed $data
     */
    private function _tryToAutoDetectTypeFormatFrom( $data )
    {
        if ( $this->_fromFormatType ) {
            return;
        }

        if ( is_string($data) && substr($data, 0, 1) == '{' && substr($data, -1) == '}' ) {
            json_decode($data);
            if ( json_last_error() === JSON_ERROR_NONE ) {
                $this->_fromFormatType = FromJson::TYPE;
            }
        }
    }

    /**
     * @param array $dataList
     * @param MapperStrategyInterface $toCaseMapper
     * @param MapperStrategyInterface $toEncodingMapper
     * @return array
     */
    private function _applyMapper( array $dataList, $toCaseMapper, $toEncodingMapper )
    {
        if (!$this->getMapper()) {
            return $dataList;
        }

        $toArray = [];

        foreach ( $dataList as $key => $value ) {

            if ( is_string($key) && is_string($value) ) {

                if ( $toCaseMapper && ($castResult = $toCaseMapper->cast([$key => $value])) ) {
                    $key = $castResult[ 'key' ];
                    $value = $castResult[ 'value' ];
                }

                if ( $toEncodingMapper && ($castResult = $toEncodingMapper->cast([$key => $value])) ) {
                    $key = $castResult[ 'key' ];
                    $value = $castResult[ 'value' ];
                }
            }
            else if ( is_array($value) ) {
                $value = $this->_applyMapper($value, $toCaseMapper, $toEncodingMapper);
            }

            $toArray[ $key ] = $value;
        }

        return $toArray;
    }

    /**
     * @param FromGenericString $formatType
     * @param string $string
     * @return array
     */
    private function _castFromGenericFormat( FromGenericString $formatType, $string )
    {
        $formatedList = [];

        if ($formatType->getElementDelimiter()) {
            $elementList = explode($formatType->getElementDelimiter(), $string);

            foreach ( $elementList as $keyValue ) {
                if ( $formatType->getKeyValueDelimiter() ) {
                    $keyValueList = explode($formatType->getKeyValueDelimiter(), $keyValue);

                    if ( count($keyValueList) > 1 ) {
                        $formatedList[ $keyValueList[ 0 ] ] = $keyValueList[ 1 ];
                    } else {
                        $formatedList[] = $keyValue;
                    }
                } else {
                    $formatedList[] = $keyValue;
                }
            }
        }
        else {
            $formatedList[] = $string;
        }

        return $formatedList;
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function _castToArray( $data )
    {
        $toArray = [];
        $dataArray = (array) $data;

        foreach ( $dataArray as $key => $value )
        {
            // If $data was a object !!!
            $key = preg_replace('/\0.*\0/', '', $key);

            if ( is_object($value) || is_array($value) ) {
                $value = $this->_castToArray($value);
            }

            $toArray[ $key ] = $value;
        }

        return $toArray;
    }

}
