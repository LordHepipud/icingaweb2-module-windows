<?php

namespace Icinga\Module\Windows\Object\Objects;

class CpuCore
{
    protected $id;

    protected $value;

    protected $help;

    protected $rawvalue;

    protected $type;

    protected $timestamp;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setHelp($help)
    {
        $this->help = $help;
    }

    public function setRawValue($rawvalue)
    {
        $this->rawvalue = $rawvalue;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setTimeStamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHelp()
    {
        return $this->help;
    }

    public function getRawValue()
    {
        return $this->rawvalue;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTimeStamp()
    {
        return $this->timestamp;
    }
}