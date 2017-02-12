<?php

namespace PhpObjects\TypeCast\Strategy;

interface StrategyInterface
{

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param mixed $data
     * @return mixed
     */
    public function cast( $data );

}