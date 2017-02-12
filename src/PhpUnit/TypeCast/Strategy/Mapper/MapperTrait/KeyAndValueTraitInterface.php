<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\MapperTrait;

interface KeyAndValueTraitInterface
{

    /**
     * @return bool
     */
    public function isMappingKey();

    /**
     * @return bool
     */
    public function isMappingValue();

    /**
     * @return bool
     */
    public function isMappingKeyAndValue();

    /**
     * @return $this
     */
    public function mapKeyOnly();

    /**
     * @return $this
     */
    public function mapValueOnly();

    /**
     * @return $this
     */
    public function mapKeyAndValue();

    /**
     * @return $this
     */
    public function unsetKeyAndValueMapping();

}
