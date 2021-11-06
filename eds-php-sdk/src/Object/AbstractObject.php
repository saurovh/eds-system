<?php

namespace Saurovh\EdsPhpSdk\Object;

use Saurovh\EdsPhpSdk\Enum\EmptyEnum;

abstract class AbstractObject
{
    /**
     * @var mixed[] set of key value pairs representing data
     */
    protected $data = [];

    public function __construct()
    {
        $this->data = static::getFieldsEnum()->getValuesMap();
    }

    protected static function getFieldTypes()
    {
        $fieldsEnum = static::getFieldsEnum();
        if (method_exists($fieldsEnum, 'getFieldTypes')) {
            return $fieldsEnum->getFieldTypes();
        } else {
            return [];
        }
    }

    protected static function getReferencedEnums()
    {
        return [];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new \InvalidArgumentException(
                $name . ' is not a field of ' . get_class($this));
        }
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param array
     *
     * @return $this
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        // Handle class-specific situations
        if (method_exists($this, 'setDataTrigger')) {
            $this->setDataTrigger($data);
        }

        return $this;
    }

    /**
     * Like setData but will skip field validation
     *
     * @param array
     *
     * @return $this
     */
    public function setDataWithoutValidation(array $data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        // Handle class-specific situations
        if (method_exists($this, 'setDataTrigger')) {
            $this->setDataTrigger($data);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function exportValue($value)
    {
        $result = $value;
        switch (true) {
            case $value === null:
                break;
            case $value instanceof AbstractObject:
                $result = $value->exportData();
                break;
            case is_array($value):
                $result = [];
                foreach ($value as $key => $sub_value) {
                    if ($sub_value !== null) {
                        $result[$key] = $this->exportValue($sub_value);
                    }
                }
                break;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        return $this->exportValue($this->data);
    }

    /**
     * @return array
     */
    public function exportAllData()
    {
        return $this->exportValue($this->data);
    }

    /**
     * @return EmptyEnum
     */
    public static function getFieldsEnum()
    {
        return EmptyEnum::getInstance();
    }

    /**
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
}