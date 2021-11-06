<?php

namespace Saurovh\EdsPhpSdk\Object;

use GuzzleHttp\Exception\InvalidArgumentException;
use Saurovh\EdsPhpSdk\Api;
use Saurovh\EdsPhpSdk\HasApiClient;
use Exception;
use Saurovh\EdsPhpSdk\Http\ResponseInterface;

abstract class AbstractCrudObject extends AbstractObject
{
    use HasApiClient;

    /**
     * @var string
     */
    const FIELD_ID = 'id';

    public function __construct($id = null, Api $api = null)
    {
        parent::__construct();

        if (!empty($id)) {
            $this->data[static::FIELD_ID] = $id;
        }
        $this->setApi($api);
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->data[static::FIELD_ID] ?? null;
    }

    public function get(array $params = []): ResponseInterface
    {
        return $this->getApi()->get($this->getEndpoint(), $params);
    }

    public function create(array $params): ResponseInterface
    {
        return $this->getApi()->post($this->getEndpoint(), $params);
    }

    /**
     * @param int|string $id
     * @param array      $params
     *
     * @return ResponseInterface
     */
    public function update($id, array $params)
    {
        $params[static::FIELD_ID] = $id;

        return $this->getApi()->put($this->getEndpoint(), $params);
    }

    /**
     * @param int|string $id
     */
    public function getById($id)
    {
        return $this->getApi()->get($this->getEndpoint() . '/' . $id);
    }

    /**
     * @param int|string $id
     */
    public function delete($id)
    {
        return $this->getApi()->delete($this->getEndpoint() . '/' . $id);
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function getSelf(): self
    {
        $this->assureId();
        $id = $this->getId();

        $response = $this->getById($id);
        if ($response->isSuccessful()) {
            $this->setData($response->getData());
        } else if ($exception = $response->getException()) {
            throw $exception;
        }

        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteSelf(): ResponseInterface
    {
        $this->assureId();
        $id = $this->getId();

        return $this->delete($id);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function assureId()
    {
        if (!isset($this->data[static::FIELD_ID])) {
            throw new InvalidArgumentException(sprintf("field %s is required.", static::FIELD_ID));
        }

        return $this->data[static::FIELD_ID];
    }

    abstract protected function getEndpoint(): string;
}