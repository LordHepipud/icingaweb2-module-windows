<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Application\Benchmark;
use Icinga\Exception\ConfigurationError;
use Exception;

class RamInfo
{
    protected $id;

    protected $slot;

    protected $model;

    protected $capacity;

    protected $version;

    protected $type;

    protected $clockspeed;

    protected $speed;

    protected $installdate;

    protected $description;

    protected $location;

    protected $caption;

    protected $minvoltage;

    protected $maxvoltage;

    protected $configuredvoltage;

    protected $manufacturer;

    protected $status;

    protected $serialnumber;

    protected $partnumber;

    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($slot)
    {
        $this->slot = $slot;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getClockSpeed()
    {
        return $this->clockspeed;
    }

    /**
     * @param mixed $clockspeed
     */
    public function setClockSpeed($clockspeed)
    {
        $this->clockspeed = $clockspeed;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @param mixed $speed
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    }

    /**
     * @return mixed
     */
    public function getInstallDate()
    {
        return $this->installdate;
    }

    /**
     * @param mixed $installdate
     */
    public function setInstallDate($installdate)
    {
        $this->installdate = $installdate;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param mixed $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return mixed
     */
    public function getMinVoltage()
    {
        return $this->minvoltage;
    }

    /**
     * @param mixed $minvoltage
     */
    public function setMinVoltage($minvoltage)
    {
        $this->minvoltage = $minvoltage;
    }

    /**
     * @return mixed
     */
    public function getMaxVoltage()
    {
        return $this->maxvoltage;
    }

    /**
     * @param mixed $maxvoltage
     */
    public function setMaxVoltage($maxvoltage)
    {
        $this->maxvoltage = $maxvoltage;
    }

    /**
     * @return mixed
     */
    public function getConfiguredVoltage()
    {
        return $this->configuredvoltage;
    }

    /**
     * @param mixed $configuredvoltage
     */
    public function setConfiguredVoltage($configuredvoltage)
    {
        $this->configuredvoltage = $configuredvoltage;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialnumber;
    }

    /**
     * @param mixed $serialnumber
     */
    public function setSerialNumber($serialnumber)
    {
        $this->serialnumber = $serialnumber;
    }

    /**
     * @return mixed
     */
    public function getPartNumber()
    {
        return $this->partnumber;
    }

    /**
     * @param mixed $partnumber
     */
    public function setPartNumber($partnumber)
    {
        $this->partnumber = $partnumber;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}