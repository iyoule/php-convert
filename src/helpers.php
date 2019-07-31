<?php


namespace iyoule\Convert;


/**
 * @param array|bool|float|int|object|string $source
 * @param string $type typename
 * @return array|bool|float|int|object|string
 * @throws \TypeError
 */
function convert_type($source, $type)
{
    try {
        return Convert::from($source)->to($type);
    } catch (\Throwable $e) {
        throw new \TypeError($e->getMessage());
    }
}


/**
 * @param $val
 * @param $type
 * @return bool|float|int|string
 * @throws Exception\ConvertException
 */
function setType($val, $type)
{
    return ConvertScalar::from($val)->to($type);
}