<?php

namespace Saurovh\EdsPhpSdk;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait HasLoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger;
        }
        return $this->logger;
    }
}