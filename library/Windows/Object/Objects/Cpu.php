<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\PerfCounter\PerfCounter;

class Cpu
{
    protected $hostname;

    protected $host_id;

    protected $json;

    protected $counter = null;

    protected $maxthreads = 0;

    protected $maxcores = 0;

    protected $cpucores = array();

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadFromDb()
    {
        $perfCounter = new PerfCounter($this->hostname);
        $perfCounter->loadCounterFromDB(array(
            '\Processor(*)\% Processor Time'
        ), array('value', 'raw_value'));

        foreach ($perfCounter->getCounters() as $counter => $value) {
            $core = new CpuCore();
            $core->setId($value->getInstance());
            $core->setValue($value->getValue());
            $core->setRawValue($value->getRawValue());
            $core->setTimeStamp($value->getTimestamp());

            $this->cpucores += array(
                $core->getId() => $core
            );
        }
    }

    public function getTotalCore()
    {
        return $this->getCoreById('_Total');
    }

    public function getCoreById($id)
    {
        if (isset($this->cpucores[$id])) {
            return $this->cpucores[$id];
        }

        return new CpuCore();
    }

    public function getCores()
    {
        return $this->cpucores;
    }

    public function parseApiRequest($content)
    {
        $perfCounter = new PerfCounter($this->hostname);
        $perfCounter->loadPerformanceCounterHelpIndex();
        $perfCounter->parsePerfCounter($content['output']);
    }
}