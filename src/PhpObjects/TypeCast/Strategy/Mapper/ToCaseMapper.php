<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\TypeCast\Strategy\Mapper\MapperTrait\KeyAndValueTrait;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\ToCaseMapperTypeInterface;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\CamelCase;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\LowerCase;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\UpperCase;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\UpperCaseFirst;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\CamelCaseByUnderscore;

class ToCaseMapper implements ToCaseMapperInterface
{

    const TYPE = 'mapper:to:case';

    /**
     * @var ToCaseMapperTypeInterface
     */
    private $_caseType;


    public function __construct()
    {}

    use KeyAndValueTrait;


    public function __toString()
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function setCase( ToCaseMapperTypeInterface $caseType )
    {
        $this->_caseType = $caseType;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cast( $data )
    {
        $wasAString = is_string($data);
        $dataList = (array) $data;

        if ( !$this->_caseType ) {
            return ($wasAString ? reset($dataList) : $dataList);
        }

        $toDataList = [];

        foreach ( $dataList as $key => $value ) {

            if ( is_string($key) && $this->isMappingKey() ) {
                $key = $this->_cast($key);
            }

            if ( is_string($value) && $this->isMappingValue() ) {
                $value = $this->_cast($value);
            }

            $toDataList[ $key ] = $value;
        }

        return ($wasAString ? reset($toDataList) : $toDataList);
    }

    /**
     * @inheritdoc
     */
    private function _cast( $string )
    {
        switch ((string) $this->_caseType)
        {
            case CamelCase::TYPE:
                return ucwords(strtolower($string));

            case UpperCase::TYPE:
                return strtoupper($string);

            case LowerCase::TYPE:
                return strtolower($string);

            case CamelCaseByUnderscore::TYPE:
                return str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $string))));

            case UpperCaseFirst::TYPE:
                return ucfirst($string);

            default:
                return $string;
        }
    }

}
