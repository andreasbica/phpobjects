<?php

namespace PhpObjects\Formatter;

interface ToCaseIntercae extends FormatterInterface
{
    const TO_CAMEL_CASE               = 0;
    const TO_UPPER_CASE               = 8;
    const TO_LOWER_CASE               = 16;
    const TO_CAMEL_CASE_BY_UNDERSCORE = 32;
    const TO_UPPER_CASE_FIRST         = 64;

    public function cast($string);
}