<?php

namespace Saurovh\EdsPhpSdk;

trait HasApiClient
{
    /**
     * @var Api instance of the Api used by this object
     */
    protected $api;

    /**
     * @return Api
     */
    public function getApi(): Api
    {
        return $this->api;
    }

    /**
     * @param Api|null $api The Api instance this object should use to make calls
     */
    public function setApi(Api $api = null): self
    {
        $this->api = static::assureApi($api);

        return $this;
    }

    /**
     * @param Api|null $instance
     *
     * @return Api
     * @throws \InvalidArgumentException
     */
    protected static function assureApi(Api $instance = null): Api
    {
        $instance = $instance ?: Api::instance();
        if (!$instance) {
            throw new \InvalidArgumentException(
                'An Api instance must be provided as argument or ' .
                'set as instance in the \Saurovh\EdsPhpSdk\Api');
        }

        return $instance;
    }
}