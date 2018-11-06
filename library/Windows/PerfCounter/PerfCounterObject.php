<?php

namespace Icinga\Module\Windows\PerfCounter;

class PerfCounterObject
{
    protected $path = '';

    protected $category  = '';

    protected $instance = '';

    protected $counter = '';

    protected $raw_value = 0;

    protected $base_value = 0;

    protected $system_frequency = 0;

    protected $counter_frequency = 0;

    protected $counter_timestamp = 0;

    protected $timestamp = 0;

    protected $timestamp_100nsec = 0;

    protected $value = 0;

    public function __construct($db_result)
    {
        if ($db_result == null) {
            return;
        }
        $this->setProperty($db_result, 'category');
        $this->setProperty($db_result, 'instance');
        $this->setProperty($db_result, 'counter');
        $this->setProperty($db_result, 'raw_value');
        $this->setProperty($db_result, 'base_value');
        $this->setProperty($db_result, 'system_frequency');
        $this->setProperty($db_result, 'counter_frequency');
        $this->setProperty($db_result, 'counter_timestamp');
        $this->setProperty($db_result, 'timestamp');
        $this->setProperty($db_result, 'timestamp_100nsec');
        $this->setProperty($db_result, 'value');
    }

    protected function setProperty($db_result, $property)
    {
        if (property_exists($db_result, $property)) {
            $this->$property = $db_result->$property;
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @return mixed
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param mixed $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->raw_value;
    }

    /**
     * @param mixed $raw_value
     */
    public function setRawValue($raw_value)
    {
        $this->raw_value = $raw_value;
    }

    /**
     * @return mixed
     */
    public function getBaseValue()
    {
        return $this->base_value;
    }

    /**
     * @param mixed $base_value
     */
    public function setBaseValue($base_value)
    {
        $this->base_value = $base_value;
    }

    /**
     * @return mixed
     */
    public function getSystemFrequency()
    {
        return $this->system_frequency;
    }

    /**
     * @param mixed $system_frequency
     */
    public function setSystemFrequency($system_frequency)
    {
        $this->system_frequency = $system_frequency;
    }

    /**
     * @return mixed
     */
    public function getCounterFrequency()
    {
        return $this->counter_frequency;
    }

    /**
     * @param mixed $counter_frequency
     */
    public function setCounterFrequency($counter_frequency)
    {
        $this->counter_frequency = $counter_frequency;
    }

    /**
     * @return mixed
     */
    public function getCounterTimestamp()
    {
        return $this->counter_timestamp;
    }

    /**
     * @param mixed $counter_timestamp
     */
    public function setCounterTimestamp($counter_timestamp)
    {
        $this->counter_timestamp = $counter_timestamp;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getTimestamp100nsec()
    {
        return $this->timestamp_100nsec;
    }

    /**
     * @param mixed $timestamp_100nsec
     */
    public function setTimestamp100nsec($timestamp_100nsec)
    {
        $this->timestamp_100nsec = $timestamp_100nsec;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}