<?php

namespace PhpObjects\Formatter;

class ToStdClass implements ToTypeInterface
{

    public function __construct()
    {
    }


    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @param  bool $forceCastObject
     * @return \stdClass
     */
    public function cast( $data, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null, $forceCastObject = false )
    {
        $data = (new ToArray)->cast($data, $keyToCase);

        if ( !$forceCastObject && $this->_isNumericArray($data) ) $toStdClass = array();
        else $toStdClass = new \stdClass();

        if ( $forceCastObject ) {
            return $this->_castForceToStdClass($data, $toStdClass, $toEncoding);
        }

        return $this->_castToStdClass($data, $toStdClass, $toEncoding);
    }


    /**
     * @see    cast
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return \stdClass
     */
    public function castForce( $data, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null )
    {
        return $this->cast($data, $keyToCase, $toEncoding, true);
    }


    /**
     * @param  array $dataList
     * @param  \stdClass|array $toStdClass
     * @param  ToEncodingMapper $toEncoding
     * @return \stdClass
     */
    protected function _castToStdClass( array $dataList, $toStdClass, $toEncoding )
    {
        foreach ( $dataList as $key => $value ) {
            if ( is_array($value) ) {
                if ( $value ) {
                    if ( $this->_isNumericArray($value) ) {
                        $toStdClass->$key = array();
                        $toStdClass->$key = $this->_castToStdClass($value, $toStdClass->$key, $toEncoding);
                    } else {
                        if ( is_array($toStdClass) ) {
                            $toStdClass[ $key ] = new \stdClass();
                            $this->_castToStdClass($value, $toStdClass[ $key ], $toEncoding);
                        } else {
                            $toStdClass->$key = new \stdClass();
                            $this->_castToStdClass($value, $toStdClass->$key, $toEncoding);
                        }
                    }
                } else {
                    if ( is_array($toStdClass) ) $toStdClass[ $key ] = null;
                    else $toStdClass->$key = null;
                }
            } else {
                if ( is_array($toStdClass) ) $toStdClass[ $key ] = ($toEncoding == null ? $value : $toEncoding->cast($value));
                else $toStdClass->$key = ($toEncoding == null ? $value : $toEncoding->cast($value));
            }
        }

        return $toStdClass;
    }


    /**
     * @param  array $dataList
     * @param  \stdClass $toStdClass
     * @param  ToEncodingMapper $toEncoding
     * @return \stdClass
     */
    protected function _castForceToStdClass( array $dataList, \stdClass $toStdClass, $toEncoding )
    {
        foreach ( $dataList as $key => $value ) {
            if ( is_array($value) ) {
                if ( $value ) {
                    $toStdClass->$key = new \stdClass();
                    $this->_castForceToStdClass($value, $toStdClass->$key, $toEncoding);
                } else {
                    $toStdClass->$key = null;
                }
            } else {
                $toStdClass->$key = ($toEncoding == null ? $value : $toEncoding->cast($value));
            }
        }

        return $toStdClass;
    }


    /**
     * @param  array $dataList
     * @return bool
     */
    protected function _isNumericArray( array $dataList )
    {
        $isNumeric = true;
        $dataKeyList = array_keys($dataList);

        foreach ( $dataKeyList as $dataKey ) {
            if ( !is_numeric($dataKey) ) {
                $isNumeric = false;
                break;
            }
        }

        return $isNumeric;
    }

}
