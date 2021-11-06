<?php

namespace App\Enums;

class HttpResponseStatus extends Enum
{
    public const HTTP_OK                    = 200;
    public const HTTP_CREATED               = 201;
    public const HTTP_BAD_REQUEST           = 400;
    public const HTTP_UNAUTHORIZED          = 401;
    public const HTTP_PAYMENT_REQUIRED      = 402;
    public const HTTP_FORBIDDEN             = 403;
    public const HTTP_NOT_FOUND             = 404;
    public const HTTP_METHOD_NOT_ALLOWED    = 405;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED       = 501;
    public const HTTP_BAD_GATEWAY           = 502;
}
