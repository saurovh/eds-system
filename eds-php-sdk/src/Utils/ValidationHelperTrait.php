<?php

namespace Saurovh\EdsPhpSdk\Utils;

use Exception;

trait ValidationHelperTrait
{
    /**
     * @param array  $ids
     * @param string $key
     *
     * @return array
     * @throws Exception
     */
    private function assureValidIds(array $ids, $key = 'ids')
    {
        if (empty($ids) || count($ids) > 100) {
            throw new Exception("$key only allowed quantity range from 1-100");
        }

        return $ids;
    }
}