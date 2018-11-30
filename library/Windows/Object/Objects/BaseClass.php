<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;

abstract class BaseClass
{
    protected $hostname;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    abstract function parseApiRequest($content);
}