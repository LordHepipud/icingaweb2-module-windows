<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\JsonParser;
use Icinga\Application\Benchmark;
use Icinga\Exception\ConfigurationError;
use Exception;

class Base
{
    protected $data;

    protected $counter = null;

    protected $os = '';

    protected $build = '';

    protected $version = '';

    protected $systemroot = '';

    protected $status = '';

    public function __construct($data)
    {
        $this->data = $data;
        $this->counter = new JsonParser($this->data);

        $this->parseData();
    }

    public function parseData()
    {
        $this->os = $this->counter->getCounterValue(
            'windows',
            'Caption'
        );

        $this->build = $this->counter->getCounterValue(
            'windows',
            'BuildNumber'
        );

        $this->version = $this->counter->getCounterValue(
            'windows',
            'Version'
        );

        $this->systemroot = $this->counter->getCounterValue(
            'windows',
            'SystemDirectory'
        );

        $this->status = $this->counter->getCounterValue(
            'windows',
            'Status'
        );
    }

    public function getOS()
    {
        return $this->os;
    }

    public function getBuildNumber()
    {
        return $this->build;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSystemRoot()
    {
        return $this->systemroot;
    }

    public function getStatus()
    {
        return $this->status;
    }
}