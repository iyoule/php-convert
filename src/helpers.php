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