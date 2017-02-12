<?php

namespace PhpObjects\DataSource;

class SqlBound
{

    private static $_UsePregMatchForBindSqlData = false;

    private $_sql     = '';
    private $_binding = [];


    /**
     * @param string $statement
     * @param array $binding
     */
    public function __construct($statement, array $binding)
    {
        $this->_sql     = $statement;
        $this->_binding = $binding;
    }


    /**
     * @param string $statement
     * @param array $binding
     * @return SqlBound
     */
    public static function create($statement, array $binding)
    {
        return new self($statement, $binding);
    }


    /**
     * @param string $sql
     * @return SqlBound
     */
    public function setSql($sql)
    {
        $this->_sql = $sql;
        return $this;
    }


    /**
     * @param bool $resolve
     * @return string
     */
    public function getSql($resolve = false)
    {
        if (!$resolve) {
            return $this->_sql;            
        }

        return $this->_bindSqlData($this->_sql, $this->_binding);
    }


    /**
     * @param  array $tableDescription
     * @return string
     */
    public function resolveSql(array $tableDescription = [])
    {
        if ($tableDescription) {
            return $this->_bindSqlData($this->_sql, $this->_binding, $tableDescription);
        }

        return $this->getSql(true);
    }


    /**
     * @param array $binding
     * @return SqlBound
     */
    public function setBinding(array $binding)
    {
        $this->_binding = $binding;
        return $this;
    }


    /**
     * @return array
     */
    public function getBinding()
    {
        return $this->_binding;
    }


    private function _makeSqlValueString($value, &$columnType)
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'" . addslashes($value) . "'";
    }


    private function _bindSqlData($sql, array $binding, array &$tableDescription = [])
    {
        foreach ($binding as $bindKey => $bindValue)
        {
            $columnType = '';

            if ($tableDescription)
            {
                foreach ($tableDescription as $column)
                {
                    if (str_replace([':', ':val_'], '', $bindKey) == $column['Field'])
                    {
                        $items = explode('(', $column['Type']);
                        $columnType = $items[0];
                    }
                }
            }

            if (self::$_UsePregMatchForBindSqlData) // Die sicherere, aber aufwendigere variante !!!
            {
                $appendChar = '';
                $pattern    = '/' . "\\" . $bindKey . '[^a-zA-Z0-9]/';

                if (preg_match($pattern, $sql, $matches) === 1)
                {
                    $bindKey    = $matches[0];
                    $appendChar = substr($bindKey, -1);
                }

                $sql = str_replace($bindKey, self::_makeSqlValueString($bindValue, $columnType).$appendChar, $sql);
            }
            else {
                // Ersetzt nur das erste vorkommen !!!
                $sql = substr_replace($sql, self::_makeSqlValueString($bindValue, $columnType), strpos($sql, $bindKey), strlen($bindKey));
            }
        }

        return $sql;
    }

}
