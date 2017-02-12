<?php

namespace PhpObjects\Formatter;

interface ToJsonInterface extends FormatterInterface
{
    public function cast($data, ToCase $stringToCase = null);
}