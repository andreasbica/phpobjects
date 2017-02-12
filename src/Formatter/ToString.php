<?php

namespace PhpObjects\Formatter;

class ToString implements ToTypeInterface
{
    protected $_toFormat = '';
    protected $_formatSeparator = '';


    /**
     * @see   cast
     * @param string $toFormat
     * @param $separator
     */
    public function __construct( $toFormat = '', $separator = '' )
    {
        $this->_toFormat = $toFormat;
        $this->_formatSeparator = $separator;
    }


    /**
     * @return ToString
     */
    public static function SERIALIZE()
    {
        return new self('serialize', '');
    }


    /**
     * @return ToString
     */
    public static function SEMICOLON()
    {
        return new self('%s=%s', ';');
    }


    /**
     * @return ToString
     */
    public static function COMMA()
    {
        return new self('%s:%s', ',');
    }


    /**
     * @return ToString
     */
    public static function PIPE()
    {
        return new self('%s = %s', ' | ');
    }


    /**
     * @return ToString
     */
    public static function CSV( $delimiter = ',', $quoted = true )
    {
        return new self('csv:' . ($quoted ? 'Y' : 'N'), $delimiter);
    }


    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @param  bool $castSpecialCharacters
     * @return string
     */
    public function cast( $data, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null, $castSpecialCharacters = false )
    {
        $toString = '';

        switch ($this->_toFormat) {
            case '':
                if ( $this->_castBoolToString($data) ) {
                    $toString = (string) $data;
                } else {
                    if ( is_object($data) ) $data = get_class($data);
                    if ( is_array($data) ) $data = 'Array';

                    $toString = (string)$data;

                    if ( $keyToCase != null ) $toString = $keyToCase->cast($toString);
                    (!$castSpecialCharacters ?: $toString = $this->castSpecialCharacters($toString, false));
                }
                break;

            case 'csv:Y':
                if ( !is_array($data) ) {
                    $data = (new ToArray)->cast($data);
                }
                $toString = '"' . implode('"' . $this->_formatSeparator . '"', $data) . '"';
                (!$castSpecialCharacters ?: $toString = $this->castSpecialCharacters($toString, false));
                break;

            case 'csv:N':
                if ( !is_array($data) ) {
                    $data = (new ToArray)->cast($data);
                }
                $toString = implode($this->_formatSeparator, $data);
                (!$castSpecialCharacters ?: $toString = $this->castSpecialCharacters($toString, false));
                break;

            case 'serialize':
                $toString = serialize($data);
                (!$castSpecialCharacters ?: $toString = $this->castSpecialCharacters($toString, false));
                break;

            default:
                if ( !is_array($data) ) {
                    $data = (new ToArray)->cast($data);
                }
                $toString = $this->_castToString($data, $keyToCase, $castSpecialCharacters);
                break;
        }

        return ($toEncoding == null ? $toString : $toEncoding->cast($toString));
    }


    /**
     * Convert mutated vowel's. i.E. � > ae, � > oe, � > Ae, ...
     * @param  string $value
     * @param  bool $revertCast
     * @return string
     */
    public function castSpecialCharacters( $value, $revertCast = false )
    {
        $searchPattern = array('�', '�', '�', '�', '�', '�', '�');
        $replacePattern = array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss');

        if ( $revertCast ) {
            return str_replace($replacePattern, $searchPattern, $value);
        }

        return str_replace($searchPattern, $replacePattern, $value);
    }


    /**
     * @param  array $dataList
     * @param  ToCase $keyToCase
     * @param  bool $castSpecialCharacters
     * @return string
     */
    protected function _castToString( array $dataList, $keyToCase, $castSpecialCharacters )
    {
        $toStringList = array();

        foreach ( $dataList as $key => $value ) {
            if ( is_object($value) ) $value = (new ToArray)->cast($value);
            if ( is_array($value) ) $value = '{' . $this->_castToString($value, $keyToCase, $castSpecialCharacters) . '}';

            $this->_castBoolToString($value);

            if ( $castSpecialCharacters ) $value = $this->castSpecialCharacters($value, false);
            if ( $keyToCase != null ) $key = $keyToCase->cast($key);

            $toStringList[] = sprintf($this->_toFormat, (string)$key, (string)$value);
        }

        return implode($this->_formatSeparator, $toStringList);
    }


    /**
     * @param  string &$value
     * @return bool
     */
    protected function _castBoolToString( &$value )
    {
        if ( is_bool($value) ) {
            $value = ($value ? 'Y' : 'N');
            return true;
        }

        return false;
    }

}
