<?php

namespace PhpObjects\Formatter;

interface ToTypeInterface extends FormatterInterface
{

    /**
     * @param mixed $data
     * @param ToCase|null $stringToCase
     * @param ToEncodingMapper|null $toEncoding
     * @return array
     */
    public function cast($data, ToCase $stringToCase = null, ToEncodingMapper $toEncoding = null);

}