<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Application\Benchmark;
use Icinga\Exception\ConfigurationError;
use Exception;

class CpuHCore
{
    protected $id;

    protected $threads;

    protected $cores;

    protected $maxclockspeed;

    protected $currentclockspeed;

    protected $currentvoltage;

    protected $description;

    protected $partnumber;

    protected $processorid;

    protected $serialnumber;

    protected $revision;

    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setThreads($threads)
    {
        $this->threads = $threads;
    }

    public function setCores($cores)
    {
        $this->cores = $cores;
    }

    public function setMaxClockSpeed($maxclockspeed)
    {
        $this->maxclockspeed = $maxclockspeed;
    }

    public function setCurrentClockSpeed($currentclockspeed)
    {
        $this->currentclockspeed = $currentclockspeed;
    }

    public function setCurrentVoltage($currentvoltage)
    {
        $this->currentvoltage = $currentvoltage;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPartNumber($partnumber)
    {
        $this->partnumber = $partnumber;
    }

    public function setProcessorId($processorid)
    {
        $this->processorid = $processorid;
    }

    public function setSerialNumber($serialnumber)
    {
        $this->serialnumber = $serialnumber;
    }

    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getThreads()
    {
        return $this->threads;
    }

    public function getCores()
    {
        return $this->cores;
    }

    public function getMaxClockSpeed()
    {
        return $this->maxclockspeed;
    }

    public function getCurrentClockSpeed()
    {
        return $this->currentclockspeed;
    }

    public function getCurrentVoltage()
    {
        return $this->currentvoltage;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPartNumber()
    {
        return $this->partnumber;
    }

    public function getProcessorId()
    {
        return $this->processorid;
    }

    public function getSerialNumber()
    {
        return $this->serialnumber;
    }

    public function getRevision()
    {
        return $this->revision;
    }
}