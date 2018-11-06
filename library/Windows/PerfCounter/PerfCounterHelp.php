<?php

namespace Icinga\Module\Windows\PerfCounter;

use Icinga\Module\Windows\WindowsDB;

class PerfCounterHelp
{

    protected $full_counters = array();

    protected $counter_index = array();

    public function loadCounterIndexFromDb()
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_perf_counter_help',
                array('counter')
            );

        $rows = $query->fetchAll();

        foreach($rows as $row) {
            $this->counter_index += array(
                strtolower($row->counter) => true
            );
        }
    }

    public function isIndexKnown($counter)
    {
        if (array_key_exists(strtolower($counter), $this->counter_index)) {
            return true;
        }

        return false;
    }

    public function loadCounterHelpFromDb()
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_perf_counter_help',
                array(
                    'counter',
                    'counter_type',
                    'type',
                    'help'
                )
            );

        $rows = $query->fetchAll();

        foreach ($rows as $row) {
            $counter = new PerfCounterHelpObject($row);
            $this->full_counters += array(
                strtolower($row->counter) => $counter
            );
        }
    }

    public function getCounterHelp($counter)
    {
        if (array_key_exists(strtolower($counter), $this->full_counters)) {
            return $this->full_counters[strtolower($counter)];
        }

        return (new PerfCounterHelpObject(null));
    }
}