<?php

namespace PhpObjects\Formatter;

interface ToEncodingMapperInterface extends FormatterInterface
{
    public function cast($string);
}