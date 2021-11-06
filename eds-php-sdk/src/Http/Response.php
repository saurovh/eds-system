<?php

namespace Saurovh\EdsPhpSdk\Http;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Exception;

class Response implements ResponseInterface
{
    /**
     * @var GuzzleResponse
     */
    protected $response;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Response constructor.
     *
     * @param GuzzleResponse $response
     * @param Exception|null $exception
     */
    public function __construct(GuzzleResponse $response, Exception $exception = null)
    {
        $this->response  = $response;
        $this->exception = $exception;
        $this->parse($response);
    }

    /**
     * @param GuzzleResponse $response
     * @param Exception|null $exception
     *
     * @return static
     */
    public static function create(GuzzleResponse $response, Exception $exception = null)
    {
        return new static($response, $exception);
    }

    protected function parse(GuzzleResponse $response)
    {
        $this->httpStatusCode = $response->getStatusCode();
        // resetting the stream pointer
        $response->getBody()->rewind();
        $responseString = $response->getBody()->getContents();
        $this->data     = $responseString ? json_decode($responseString, true) : [];
        $this->message  = $response->getReasonPhrase();
    }

    public function isRequestSuccessful(): bool
    {
        return $this->httpStatusCode === 200 || $this->httpStatusCode === 201;
    }

    public function isSuccessful(): bool
    {
        return $this->isRequestSuccessful();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @return GuzzleResponse
     */
    public function getResponse(): ?GuzzleResponse
    {
        return $this->response;
    }

    /**
     * @return Exception
     */
    public function getException(): ?Exception
    {
        return $this->exception;
    }

    public function hasException(): bool
    {
        return !empty($this->exception);
    }
}