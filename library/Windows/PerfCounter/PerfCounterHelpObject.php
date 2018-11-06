<?php

namespace Icinga\Module\Windows\PerfCounter;

class PerfCounterHelpObject
{
    protected $counter = '';

    protected $counter_type  = 0;

    protected $type = '';

    protected $help = '';

    public function __construct($db_result)
    {
        if ($db_result == null) {
            return;
        }
        $this->setProperty($db_result, 'counter');
        $this->setProperty($db_result, 'counter_type');
        $this->setProperty($db_result, 'type');
        $this->setProperty($db_result, 'help');
    }

    protected function setProperty($db_result, $property)
    {
        if (property_exists($db_result, $property)) {
            $this->$property = $db_result->$property;
        }
    }

    /**
     * @return string
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param string $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @return int
     */
    public function getCounterType()
    {
        return $this->counter_type;
    }

    /**
     * @param int $counter_type
     */
    public function setCounterType($counter_type)
    {
        $this->counter_type = $counter_type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param string $help
     */
    public function setHelp($help)
    {
        $this->help = $help;
    }
}