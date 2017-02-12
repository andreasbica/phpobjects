<?php

namespace PhpObjects\Formatter;

class ToArray implements ToArrayInterface
{

    public function __construct()
    {
    }
    

    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return array
     */
    public function cast( $data, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null )
    {
        return $this->_castToArray($data, $keyToCase, $toEncoding);
    }


    /**
     * @param  string $jsonString
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return array
     */
    public function castFromJson( $jsonString, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null )
    {
        $dataArray = ($jsonString ? json_decode($jsonString, true) : []);
        return $this->_castToArray($dataArray, $keyToCase, $toEncoding);
    }


    /**
     * @param  mixed $data
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return array
     */
    protected function _castToArray( $data, $keyToCase, $toEncoding )
    {
        $toArray = [];
        $dataArray = (array) $data;

        foreach ( $dataArray as $key => $value ) {
            $key = preg_replace('/\0.*\0/', '', $key); // If $data was a object !!!

            if ( $keyToCase != null ) $key = $keyToCase->cast($key);
            if ( $toEncoding != null ) $key = $toEncoding->cast($key);

            if ( is_object($value) || is_array($value) ) {
                $value = $this->_castToArray($value, $keyToCase, $toEncoding);
            } else {
                if ( $toEncoding != null ) $value = $toEncoding->cast($value);
            }

            $toArray[ $key ] = $value;
        }

        return $toArray;
    }

}
