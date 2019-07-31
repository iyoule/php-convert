<?php

namespace iyoule\Convert;

class ConvertScalar
{
    const BOOL = 'bool';
    const FLOAT = 'float';
    const INT = 'int';
    //const ARRAY = 'array';
    const STRING = 'string';

    private $data;

    private function __construct($data)
    {
        $this->data = $data;
    }


    public static function from($source)
    {
        return new static($source);
    }

    /**
     * @param $type
     * @return array|bool|float|int|string
     */
    public function to($type)
    {
        switch ($type) {
            case 'string':
                return (string)$this->data;
            case 'int':
            case 'integer':
                return (int)$this->data;
            case 'float':
            case 'double':
                return floor($this->data);
                break;
            case 'bool':
            case 'boolean':
                return boolval($this->data);
//            case 'array':
//                return $this->data === null ? [] : array($this->data);
            default:
                trigger_error("Uncaught Error: ValueType '{$type}' is not scalar", E_USER_ERROR);
        }
    }


    public static function isScalar($val)
    {
        return is_scalar($val);
    }


}