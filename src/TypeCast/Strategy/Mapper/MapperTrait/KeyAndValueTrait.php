<?php

namespace PhpObjects\TypeCast\Strategy\Mapper\MapperTrait;

trait KeyAndValueTrait
{

    private $_keyToCase = false;
    private $_valueToCase = false;


    /**
     * @inheritdoc
     */
    public function isMappingKey()
    {
        return $this->_keyToCase;
    }

    /**
     * @inheritdoc
     */
    public function isMappingValue()
    {
        return $this->_valueToCase;
    }

    /**
     * @inheritdoc
     */
    public function isMappingKeyAndValue()
    {
        return ($this->_keyToCase && $this->_valueToCase ? true : false);
    }

    /**
     * @inheritdoc
     */
    public function mapKeyOnly()
    {
        $this->_keyToCase = true;
        $this->_valueToCase = false;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mapValueOnly()
    {
        $this->_keyToCase = false;
        $this->_valueToCase = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mapKeyAndValue()
    {
        $this->_keyToCase = true;
        $this->_valueToCase = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unsetKeyAndValueMapping()
    {
        $this->_keyToCase = false;
        $this->_valueToCase = false;
        return $this;
    }

}
