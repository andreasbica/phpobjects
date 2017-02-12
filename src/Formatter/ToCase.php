<?php

namespace PhpObjects\Formatter;

class ToCase implements ToCaseIntercae
{
    /**
     * @var int
     */
    protected $_caseMode;


    /**
     * @param int $caseMode
     */
    protected function __construct( $caseMode )
    {
        $this->_caseMode = $caseMode;
    }


    /**
     * @return $this
     */
    public static function CAMEL_CASE()
    {
        return new self(self::TO_CAMEL_CASE);
    }


    /**
     * @return $this
     */
    public static function UPPER_CASE()
    {
        return new self(self::TO_UPPER_CASE);
    }


    /**
     * @return $this
     */
    public static function LOWER_CASE()
    {
        return new self(self::TO_LOWER_CASE);
    }


    /**
     * @return $this
     */
    public static function CAMEL_CASE_BY_UNDERSCORE()
    {
        return new self(self::TO_CAMEL_CASE_BY_UNDERSCORE);
    }


    /**
     * @return $this
     */
    public static function UPPER_FIRST()
    {
        return new self(self::TO_UPPER_CASE_FIRST);
    }


    /**
     * @param  string $string
     * @return string
     */
    public function cast( $string )
    {
        if ( !is_string($string) ) {
            return $string;
        }

        switch ($this->_caseMode) {
            case self::TO_CAMEL_CASE:
                return ucwords(strtolower($string));
            case self::TO_UPPER_CASE:
                return strtoupper($string);
            case self::TO_LOWER_CASE:
                return strtolower($string);
            case self::TO_CAMEL_CASE_BY_UNDERSCORE:
                return str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $string))));
            case self::TO_UPPER_CASE_FIRST:
                return ucfirst($string);
            default:
                return $string;
        }
    }

}
