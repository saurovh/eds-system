<?php

namespace App\Enums;

abstract class Enum
{
    /**
     * @var array|null
     */
    protected $map;

    /**
     * @var array|null
     */
    protected $names;

    /**
     * @var array|null
     */
    protected $values;

    /**
     * @var array|null
     */
    protected $valuesMap;

    /**
     * @var Enum[]|static[]
     */
    protected static $instances = array();

    /**
     * @return Enum|static
     */
    public static function getInstance()
    {
        $fqn = get_called_class();
        if (!array_key_exists($fqn, static::$instances)) {
            static::$instances[$fqn] = new static();
        }

        return static::$instances[$fqn];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->map === null) {
            $this->map = (new \ReflectionClass(get_called_class()))
                ->getConstants();
        }

        return $this->map;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        if ($this->names === null) {
            $this->names = array_keys($this->toArray());
        }

        return $this->names;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        if ($this->values === null) {
            $this->values = array_values($this->toArray());
        }

        return $this->values;
    }

    /**
     * @return array
     */
    public function getValuesMap()
    {
        if ($this->valuesMap === null) {
            $this->valuesMap = array_fill_keys($this->toArray(), null);
        }

        return $this->valuesMap;
    }

    /**
     * @param string|int|float $name
     * @return bool
     */
    public function isValid($name)
    {
        return array_key_exists($name, $this->toArray());
    }

    /**
     * @param string|int|float $value
     * @return bool
     */
    public function isValidValue($value)
    {
        return array_key_exists($value, $this->getValuesMap());
    }
}
