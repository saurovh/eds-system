<?php

namespace App\Enums;

class AppConstants extends Enum
{
    const DEFAULT_DATA_CACHE_IN_MINUTE = 60;
    const MAX_PER_PAGE                 = 100;
    const DEFAULT_PER_PAGE             = 15;

    const ZULU_DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    const MYSQL_WHERE_IN_QUERY_LIMIT = 200;
}
