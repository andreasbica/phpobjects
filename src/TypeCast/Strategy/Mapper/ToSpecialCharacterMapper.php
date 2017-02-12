<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

use PhpObjects\TypeCast\Strategy\Mapper\MapperTrait\KeyAndValueTrait;

class ToSpecialCharacterMapper implements ToSpecialCharacterMapperInterface
{

    const TYPE = 'mapper:to:specialchars';

    private $_pattern = [];
    private $_reversePattern = false;


    public function __construct()
    {
        $this->_pattern = [
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
        ];
    }

    use KeyAndValueTrait;

    
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
    public function setPattern( array $patternList )
    {
        $this->_pattern = $patternList;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * @inheritdoc
     */
    public function flipPattern()
    {
        $this->_reversePattern = !$this->_reversePattern;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPatternFliped()
    {
        return $this->_reversePattern;
    }

    /**
     * @inheritdoc
     */
    public function cast( $string )
    {
        $string = (string) $string;

        if ( !$this->getPattern() ) {
            return $string;
        }

        $searchPattern = array_keys($this->getPattern());
        $replacePattern = array_values($this->getPattern());

        if ( count($searchPattern) != count($replacePattern) ) {
            throw new \Exception(__METHOD__.': Count of array elements must be same.');
        }

        if ( $this->isPatternFliped() ) {
            return str_replace($replacePattern, $searchPattern, $string);
        }

        return str_replace($searchPattern, $replacePattern, $string);
    }

}
