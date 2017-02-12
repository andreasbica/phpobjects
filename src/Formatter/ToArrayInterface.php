<?php

namespace PhpObjects\Formatter;

interface ToArrayInterface extends ToTypeInterface
{

    /**
     * @param  string $jsonString
     * @param  ToCase $keyToCase
     * @param  ToEncodingMapper $toEncoding
     * @return array
     */
    public function castFromJson( $jsonString, ToCase $keyToCase = null, ToEncodingMapper $toEncoding = null );

}
