<?php

namespace Saurovh\EdsPhpSdk\Http;

use GuzzleHttp\Psr7\Response;
use Exception;

interface ResponseInterface
{
    public function isRequestSuccessful(): bool;

    public function isSuccessful(): bool;

    public function getData(): array;

    /**
     * @return mixed
     */
    public function getMessage();

    /**
     * @return int
     */
    public function getHttpStatusCode();


    public function getResponse(): ?Response;

    public function getException(): ?Exception;

    public function hasException(): bool;
}