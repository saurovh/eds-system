<?php

namespace Saurovh\EdsPhpSdk\Object;

class EmployeeDuty extends AbstractCrudObject
{
    protected $endpoint = 'employee-duties';

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }
}