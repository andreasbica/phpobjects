<?php

namespace PhpObjects\TypeCast\Strategy\Mapper;

interface ToSpecialCharacterMapperInterface extends MapperStrategyInterface
{

    /**
     * @param array $patternList
     * @return $this
     */
    public function setPattern( array $patternList );

    /**
     * @return array
     */
    public function getPattern();

    /**
     * @return $this
     */
    public function flipPattern();

    /**
     * @return bool
     */
    public function isPatternFliped();

}
