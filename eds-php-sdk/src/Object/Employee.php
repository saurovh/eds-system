<?php

namespace Saurovh\EdsPhpSdk\Object;

/**
 * Class Employee
 * @package Saurovh\EdsPhpSdk\Object
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $joiningDate
 */
class Employee extends AbstractCrudObject
{
    protected $endpoint = 'employees';

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

}