<?php

namespace Saurovh\EdsPhpSdk\Http;

use GuzzleHttp\Psr7\Request as GuzzleRequest;

class Request extends GuzzleRequest
{
    /**
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    const METHOD_PUT = 'PUT';
}