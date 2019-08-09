<?php

namespace iyoule\Convert;

use iyoule\Convert\Exception\ConvertException;

class ConvertScalar
{
    const BOOL = 'bool';
    const FLOAT = 'float';
    const INT = 'int';
    //const ARRAY = 'array';
    const STRING = 'string';

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public static function from($source)
    {
        return new static($source);
    }

    /**
     * @param $type
     * @return bool|float|int|string
     * @throws ConvertException
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
                return (float)($this->data);
                break;
            case 'bool':
            case 'boolean':
                return boolval($this->data);
//            case 'array':
//                return $this->data === null ? [] : array($this->data);
            default:
                throw new ConvertException("Uncaught Error: ValueType '{$type}' is not scalar");
        }
    }


    public static function isScalar($val)
    {
        return is_scalar($val);
    }


}