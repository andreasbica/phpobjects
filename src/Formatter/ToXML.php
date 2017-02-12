<?php

namespace PhpObjects\Formatter;

use PhpObjects\Core\DateTime;

class ToXML implements ToTypeInterface
{
    protected $_toFormat = array();
    protected $_lineBreak = '';
    protected $_indentMultiplier = 0;
    protected $_toEncoding = null;


    /**
     * Object factory to create your own XML formatting.
     *
     * ?type   = Try to detect the data type (string, decimal, integer, boolean, date, time, dateTime).
     * !indent = Insert current indent space depth and new line between defined elements.
     * |       = Section separator.
     * &?      = Optional element separator.
     *  In between &? section:
     *   !iterator  = Use the defined value iterator tag (<xs:element name="" type="">value</xs:element>).
     *   !iterator/ = Use the defined iterator tag and convert tmo an empty element (<xs:element name="" type="" />).
     * %s      = Placeholder for the data string.
     *
     * @example
     * $toFormat can be a string like this:
     *  '<starttag>|<iteratortag key="%s" type="xs:?type">%s</iteratortag>|</starttag>'
     *
     * or an array like this:
     *  $toFormat = array(
     *   '<starttag>',
     *   '<iteratortag key="%s" type="xs:?type">%s</iteratortag>',
     *   '</starttag>'
     *  );
     *
     * @see    create
     * @param  string|array $toFormat
     * @param  string $lineBreak
     * @param  int $indentMultiplier Indents are always spaces.
     * @throws \Exception
     */
    public function __construct( $toFormat, $lineBreak = PHP_EOL, $indentMultiplier = 4 )
    {
        if ( !is_array($toFormat) ) {
            $toFormat = explode('|', $toFormat);
        }

        if ( !count($toFormat) == 3 ) {
            throw new \Exception('Invalid parameter format $toFormat. See documentation by function create.');
        }

        if ( !is_array($toFormat[ 1 ]) ) {
            $toFormat[ 1 ] = explode('&?', $toFormat[ 1 ]);
        }

        $this->_toFormat = $toFormat;
        $this->_lineBreak = $lineBreak;
        $this->_indentMultiplier = $indentMultiplier;
    }


    /**
     * $toFormat:         '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<data>|<item key="%s">%s</item>|</data>'
     * $lineBreak:        PHP_EOL
     * $indentMultiplier: 4
     *
     * @return ToXML
     */
    public static function SIMPLE()
    {
        return new self('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<data>|<item name="%s">%s</item>|</data>', PHP_EOL, 4);
    }


    /**
     * Format an array to a simple XML.
     *
     * $toFormat:         '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<data>|<item key="%s" type="xs:?type">%s</item>|</data>'
     * $lineBreak:        PHP_EOL
     * $indentMultiplier: 4
     *
     * @return ToXML
     */
    public static function SIMPLE_WITH_TYPE()
    {
        return new self('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<data>|<item name="%s" type="xs:?type">%s</item>|</data>', PHP_EOL, 4);
    }


    public static function COMPLEX_XS()
    {
        $toFormat = array(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">',
            '<xs:element name="%s" type="xs:?type">%s&?<xs:complexType>!indent<xs:sequence>!iterator</xs:sequence>!indent</xs:complexType>&?</xs:element>',
            '</xs:schema>',
        );

        return new self($toFormat, PHP_EOL, 4);
    }


    private static function COMPLEX_WSDL_STRUCT()
    {
        $toFormat = '|<xs:element name="%s" type="xs:?type">&?<xs:complexType>!indent<xs:sequence>!iterator/</xs:sequence>!indent</xs:complexType>&?</xs:element>|';
        return new self($toFormat, PHP_EOL, 4);
    }


    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return string
     */
    public function cast( $data, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null )
    {
        if ( !is_array($data) ) {
            $data = (new ToArray)->cast($data);
        }

        if ( $toEncoding == null ) {
            $toEncoding = $this->_tryToDetectEncoding();
        }

        $toXML = $this->_castToXml($data, $keyToCase);
        return ($toEncoding == null ? $toXML : $toEncoding->cast($toXML));
    }


    /**
     * @return null|ToEncodingMapper
     */
    protected function _tryToDetectEncoding()
    {
        $haystack = $this->_toFormat[ 0 ];
        $result = preg_match('/<\?xml .* encoding.?=.?"(.*)".*\?>/', $haystack, $matchList);

        if ( $result !== false ) {
            $encoding = $matchList[ 1 ];
            return (new ToEncodingMapper($encoding));
        }

        return null;
    }


    /**
     * @param  array $dataList
     * @param  ToCase $keyToCase
     * @return string
     * @throws \Exception
     */
    protected function _castToXml( array $dataList, $keyToCase )
    {
        $indent = $this->_indentMultiplier;
        $iteratorElement = $this->_toFormat[ 1 ][ 0 ];
        $iteratorElementOpt = '';

        if ( count($this->_toFormat[ 1 ]) == 3 ) {
            $iteratorElement = $this->_toFormat[ 1 ][ 0 ] . $this->_toFormat[ 1 ][ 2 ];
            $iteratorElementOpt = $this->_toFormat[ 1 ][ 1 ];
        } else if ( count($this->_toFormat[ 1 ]) == 2 ) {
            $iteratorElement = $this->_toFormat[ 1 ][ 0 ];
            $iteratorElementOpt = $this->_toFormat[ 1 ][ 1 ];
        }

        if ( $iteratorElementOpt && strpos($iteratorElementOpt, '!iterator') === false ) {
            throw new \Exception('It is necessary to define "!iterator" or "!iterator/". See documentation by function create.');
        }

        $detectXsdType = (strpos($iteratorElement, '?type') !== false ? true : false);

        $xmlStream = $this->_toFormat[ 0 ] . $this->_lineBreak;
        $xmlStream .= $this->_iterateDataElement($dataList, $iteratorElement, $iteratorElementOpt, $indent, $detectXsdType, $keyToCase);
        $xmlStream .= $this->_toFormat[ 2 ] . $this->_lineBreak;

        return $xmlStream;
    }


    /**
     * @param  array $dataList
     * @param  string $iteratorElement
     * @param  string $iteratorElementOpt
     * @param  integer $indentMultiplier
     * @param  bool $detectXsdType
     * @param  ToCase $keyToCase
     * @return string
     */
    protected function _iterateDataElement( array $dataList, $iteratorElement, $iteratorElementOpt, $indentMultiplier, $detectXsdType, $keyToCase )
    {
        $toXml = '';
        $indent = $this->_getIndent($indentMultiplier);

        foreach ( $dataList as $key => $value ) {
            $_iteratorElement = preg_replace('/ type.?=.?".*"/', '', $iteratorElement);
            $_iteratorElementOpt = '';

            if ( is_array($value) ) {
                if ( $iteratorElementOpt ) {
                    $_iteratorElementOpt = $iteratorElementOpt;
                    $_indentMultiplier = $this->_prepareOptionalIteratorElement($_iteratorElementOpt, $indentMultiplier);
                } else {
                    $_indentMultiplier = $indentMultiplier;
                }

                $value = $this->_iterateDataElement($value, $iteratorElement, $iteratorElementOpt, ($_indentMultiplier + $this->_indentMultiplier), $detectXsdType, $keyToCase);

                if ( $value != '' ) {
                    if ( $_iteratorElementOpt ) {
                        $value = $this->_lineBreak . $value . $this->_getIndent($_indentMultiplier);
                        $value = $this->_lineBreak . str_replace('!iterator', $value, $_iteratorElementOpt);
                    } else {
                        $value = $this->_lineBreak . $value . $indent;
                    }
                }
            } else if ( $detectXsdType ) {
                $xsdType = $this->_detectDataType($value);
                $_iteratorElement = str_replace('?type', $xsdType, $iteratorElement);
            }

            if ( $keyToCase != null ) $key = $keyToCase->cast($key);
            $toXml .= $indent . sprintf($_iteratorElement, (string)$key, (string)$value) . $this->_lineBreak;
        }

        return $toXml;
    }


    /**
     * @param  string $xmlElement
     * @return string
     */
    protected function _convertToEmptyElement( $xmlElement )
    {
        $elementItems = explode('>', $xmlElement);
        return $elementItems[ 0 ] . ' />';
    }


    /**
     * IN USE !!!
     * @param  string $iteratorElementOpt
     * @param  int $indentMultiplier
     * @return int
     */
    protected function _prepareOptionalIteratorElement( &$iteratorElementOpt, $indentMultiplier )
    {
        $_indentMultiplier = $indentMultiplier;
        $indentMultiplier += $this->_indentMultiplier;

        $indentRoot = $this->_getIndent($_indentMultiplier);
        $indentNext = $this->_getIndent($indentMultiplier);

        if ( strpos($iteratorElementOpt, '!indent') !== false ) {
            $indentMultiplier += $this->_indentMultiplier;

            # First!
            # Indent start elements!
            #
            $iteratorElementOpt = preg_replace('/\!indent/', $this->_lineBreak . $this->_getIndent($indentMultiplier), $iteratorElementOpt, 1);

            # Second!
            # Indent end elements!
            #
            $iteratorElementOpt = $indentNext . preg_replace('/\!indent/', $this->_lineBreak . $indentNext, $iteratorElementOpt, 1) . $this->_lineBreak . $indentRoot;
        } else {
            $iteratorElementOpt = $indentNext . $iteratorElementOpt . $this->_lineBreak . $indentRoot;
        }

        return $indentMultiplier;
    }

    /**
     * PROTOTYPE !!!
     * @param  string $iteratorElementOpt
     * @param  int $indentMultiplier
     * @return int
     */
    protected function __PROTOTYPE__prepareOptionalIteratorElement( &$iteratorElementOpt, $indentMultiplier )
    {
        $_indentMultiplier = $indentMultiplier;
        $indentMultiplier += $this->_indentMultiplier;

        if ( strpos($iteratorElementOpt, '!indent') !== false ) {
            $indentReplacementCount = (substr_count($iteratorElementOpt, '!indent') / 2);

            for ( $i = 0; $i < $indentReplacementCount; $i++ ) {
                $indentMultiplier += $this->_indentMultiplier;
                $iteratorElementOpt = preg_replace('/\!indent/', $this->_lineBreak . $this->_getIndent($indentMultiplier), $iteratorElementOpt, 1);
            }

            for ( $i = 0; $i < $indentReplacementCount; $i++ ) {
                $_indentMultiplier += $this->_indentMultiplier;
                $indent = $this->_getIndent($indentMultiplier);
                $iteratorElementOpt = $indent . preg_replace('/\!indent/', $this->_lineBreak . $indent, $iteratorElementOpt, 1) . $this->_lineBreak . $this->_getIndent(($indentMultiplier - $this->_indentMultiplier));
            }
        } else {
            $indentRoot = $this->_getIndent($_indentMultiplier);
            $indentNext = $this->_getIndent($indentMultiplier);

            $iteratorElementOpt = $indentNext . $iteratorElementOpt . $this->_lineBreak . $indentRoot;
        }

        #echo($iteratorElementOpt);
        return $indentMultiplier;
    }


    /**
     * @param  integer $indentMultiplier
     * @return string
     */
    protected function _getIndent( $indentMultiplier )
    {
        if ( $indentMultiplier > 0 ) {
            return str_repeat(' ', $indentMultiplier);
        }

        return '';
    }


    /**
     * @param  mixed &$value
     * @return string
     */
    protected function _detectDataType( &$value )
    {
        # Simple XS Data Types: string, decimal, integer, float, boolean, date, time
        #
        switch (true) {
            case is_bool($value):
                $value = (new ToString)->cast($value);
                return 'boolean';

            case is_numeric($value):
                switch (true) {
                    case is_double($value):
                        return 'decimal';
                    case is_float($value):
                        return 'float';
                    default:
                        return 'integer';
                }

            case DateTime::checkDateTime($value):
                return 'dateTime';

            case DateTime::checkDate($value):
                return 'date';

            case DateTime::checkTime($value):
                return 'time';

            default:
                return 'string';
        }
    }

}
