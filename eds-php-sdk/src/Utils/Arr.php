<?php

namespace Saurovh\EdsPhpSdk\Utils;

class Arr
{
    /**
     * Get a subset of the items from the given array.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * json encodes an array skips the first dimension
     *
     * @param array $array
     *
     * @return array
     */
    public static function jsonEncodeSkipFirst(array $array): array
    {
        $data = [];
        foreach ($array as $key => $value) {
            $data[$key] = is_string($value) ? $value : json_encode($value);
        }
        
        return $data;
    }
}