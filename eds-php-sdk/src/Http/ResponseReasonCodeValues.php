<?php

namespace Saurovh\EdsPhpSdk\Http;

use Saurovh\EdsPhpSdk\Enum\AbstractEnum;

/**
 * Class ResponseReasonCodeValues
 */
class ResponseReasonCodeValues extends AbstractEnum
{
    const OK                     = 0;
    const PARAMETER_ERROR        = 40001;
    const PERMISSION_ERROR       = 40002;
    const SQL_FILTER_FIELD_ERROR = 40003;
    const SYS_ERROR              = 50000;
}