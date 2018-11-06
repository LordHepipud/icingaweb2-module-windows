<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\PerfCounter\PerfCounter;

class Memory
{
    protected $hostname;

    protected $freeMemory = 0;

    protected $totalMemory = 0;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadFromDb()
    {
        $perfCounter = new PerfCounter($this->hostname);
        $perfCounter->loadCounterFromDB(array(
            '\Memory\Available Bytes',
            '\Memory\Physical Memory Total Bytes'
        ), array('value'));

        $this->freeMemory = $perfCounter->getCounter('\Memory\Available Bytes')->getValue();
        $this->totalMemory = $perfCounter->getCounter('\Memory\Physical Memory Total Bytes')->getValue();
    }

    public function getTotalMemory()
    {
        return $this->totalMemory;
    }

    public function getFreeMemory()
    {
        return $this->freeMemory;
    }

    public function getUsedMemory()
    {
        return ($this->totalMemory - $this->freeMemory);
    }

    public function parseApiRequest($content)
    {
        $perfCounter = new PerfCounter($this->hostname);
        $perfCounter->loadPerformanceCounterHelpIndex();
        $perfCounter->parsePerfCounter($content['output']);
    }
}