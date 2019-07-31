<?php

namespace iyoule\Convert;


use \ReflectionException;

class Convert
{

    private $source;
    private static $_propertyCache = [];

    public function __construct($source)
    {
        $this->source = $source;
    }


    public static function from($source)
    {
        return new static($source);
    }

    /**
     * @param $type
     * @return array|bool|float|int|object|string
     * @throws ReflectionException
     */
    public function to($type)
    {
        if ($this->isScalar($type)) {
            return $this->toScalar($type, $this->source);
        } else if (is_array($this->source)) {
            return $this->byArray($type, $this->source);
        } else if (is_object($this->source)) {
            return $this->byObject($type, $this->source);
        } else {
            return $this->newInstance($type);
        }
    }


    /**
     * @param $className
     * @param $object
     * @return array|object
     * @throws ReflectionException
     */
    private function byObject($className, $object)
    {
        if (is_a($object, $className)) {
            return clone $object;
        }
        return $this->byArray($className, $object);
    }

    /**
     * @param $className
     * @param array $array
     * @return array|object
     * @throws ReflectionException
     */
    private function byArray($className, array $array)
    {
        list($className) = $ary = explode('[', $className);

        $isAry = isset($ary[1]);
        if ($isAry) {
            foreach ($array as $key => $item) {
                if (is_numeric($key)) unset($array[$key]);
            }
        }
        $reflectClass = new \ReflectionClass($className);

        if ($isAry) {
            $data = [];
            foreach ($array as $item) {
                $data[] = $this->newInstanceInitPropertyWithoutConstructor($reflectClass, $item);
            }
            return $data;
        } else {
            return $this->newInstanceInitPropertyWithoutConstructor($reflectClass, $array);
        }
    }

    /**
     * @param $className
     * @param null $ref
     * @return object
     * @throws ReflectionException
     */
    private function newInstance($className, &$ref = null)
    {
        if (!class_exists($className)) {
            trigger_error("Uncaught Error: Class '{$className}' not found", E_USER_ERROR);
        }
        $ref = new \ReflectionClass($className);
        return $this->newInstanceInitPropertyWithoutConstructor($ref);
    }


    /**
     * @param \ReflectionClass $reflectionClass
     * @param array $data
     * @return object
     * @throws ReflectionException
     */
    private function newInstanceInitPropertyWithoutConstructor(\ReflectionClass $reflectionClass, $data = [])
    {
        $object = $reflectionClass->newInstanceWithoutConstructor();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $type = $this->getPropertyType($property);
            $value = $data[$property->getName()] ?? null;
            if ($type !== null) {
                if ($type == 'resource' && !is_resource($value)) {
                    throw new ConvertException('value is most resource');
                } elseif ($this->isScalar($type)) {
                    $value = isset($value) ? ConvertScalar::from($value)->to($type) : null;
                } elseif ($type === 'array') {
                    $value = is_array($value) ? $value : is_null($value) ? [] : [$value];
                } elseif (!empty($type) && $value) {
                    $value = self::from($value)->to($type);
                }
            }
            $property->setValue($object, $value);
        }
        return $object;
    }


    private function isScalar($type)
    {
        return in_array($type, ['string', 'int', 'integer', 'float', 'double', 'bool', 'boolean']);
    }


    private function toScalar($type, $val)
    {
        return ConvertScalar::from($val)->to($type);
    }


    private function getPropertyType(\ReflectionProperty $property)
    {
        $reflectClass = $property->getDeclaringClass();
        $name = $reflectClass->getName() . '::' . $property->getName();
        $type = self::$_propertyCache[$name] ?? false;
        if ($type === false) {
            $doc = $property->getDocComment();
            if ($doc !== false) {
                if (preg_match("#@var\s+([^\s]*)#i", $doc, $ary)) {
                    $type = $ary[1];
                }
            } else {
                $type = false;
            }
            self::$_propertyCache[$name] = $type;
        }
        return $type;
    }
}