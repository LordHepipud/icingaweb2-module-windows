<?php

namespace Icinga\Module\Windows\PerfCounter;

use Icinga\Data\Filter\Filter;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Module\Windows\Helper\DbHelper;

class PerfCounter
{
    protected $counters = array();

    protected $references = array();

    protected $hostname;

    protected $host_id;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * @var PerfCounterHelp
     */
    protected $counter_help = null;

    /**
     * @var WindowsDB
     */
    protected $db;

    public function __construct($hostname)
    {
        $this->db = WindowsDB::fromConfig();
        $this->dbHelper = DbHelper::getInstance();
        $this->hostname = $hostname;
        $this->host_id = $this->dbHelper->getHostIdByName($hostname);
    }

    public function compileReferenceCounterPath($reference, $counter)
    {
        return ($reference . '\\' . $counter);
    }

    public function compilePerformanceCounterPath($category, $instance, $counter)
    {
        $path = ('\\' . $category . '\\' . $counter);

        if ($instance != '') {
            $path = ('\\' . $category . '(' . $instance . ')\\' . $counter);
        }

        return $path;
    }

    public function addPerformanceCounterHelp($counterHelp)
    {
        $this->counter_help = $counterHelp;
    }

    public function loadPerformanceCounterHelpIndex()
    {
        $this->counter_help = new PerfCounterHelp();
        $this->counter_help->loadCounterIndexFromDb();
    }

    public function loadReferenceCounterFromDB($reference, $columns)
    {
        $columns = array_merge(
            $columns,
            array(
                'counter',
                'reference'
            )
        );

        $query = $this->db->select()
            ->from(
                'host_perf_counter',
                $columns
            )->where(
                'host_id',
                $this->host_id
            )->where(
                'reference',
                $reference
            );

        $rows = $query->fetchAll();

        foreach ($rows as $row) {
            $perfCounter = new PerfCounterObject($row);

            $this->references += array(
                strtolower($reference . '\\' . $row->counter) => $perfCounter
            );
        }
    }

    public function loadCounterFromDB($counter, $columns)
    {
        $columns = array_merge(
            $columns,
            array(
                'category',
                'instance',
                'counter'
            )
        );

        foreach ($counter as $path) {

            $query = $this->db->select()
                ->from(
                    'host_perf_counter',
                    $columns
                )->where(
                    'host_id',
                    $this->host_id
                );


            $counter_name = $this->parseCounterName($path);
            $query->where(
                'category',
                $counter_name['category']
            )->where(
                'counter',
                $counter_name['counter']
            );

            if ($counter_name['instance'] != '*' && $counter_name['instance'] != '') {
                $query->where(
                    'instance',
                    $counter_name['instance']
                );
            }

            $rows = $query->fetchAll();

            foreach ($rows as $row) {
                $real_counter_path = ('\\' . $row->category . '\\' . $row->counter);
                if ($row->instance != '') {
                    $real_counter_path = ('\\' . $row->category . '(' . $row->instance . ')\\' . $row->counter);
                }

                $perfCounter = new PerfCounterObject($row);
                $perfCounter->setPath($real_counter_path);

                $this->counters += array(
                    strtolower($real_counter_path) => $perfCounter
                );
            }
        }
    }

    public function hasCounter($counter_path)
    {
        if(array_key_exists(strtolower($counter_path), $this->counters)) {
            return true;
        }

        return false;
    }

    public function getCounter($counter_path)
    {
        if(array_key_exists(strtolower($counter_path), $this->counters)) {
            return $this->counters[strtolower($counter_path)];
        }

        return (new PerfCounterObject(null));
    }

    public function getCounters()
    {
        return $this->counters;
    }

    public function hasReference($reference)
    {
        if(array_key_exists(strtolower($reference), $this->references)) {
            return true;
        }

        return false;
    }

    public function getReference($reference)
    {
        if(array_key_exists(strtolower($reference), $this->references)) {
            return $this->references[strtolower($reference)];
        }

        return (new PerfCounterObject(null));
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function parsePerfCounter($output, $reference = '')
    {
        if (is_array($output) == false) {
            return;
        }
        $counter = $output;

        foreach ($counter as $path => $data) {
            if ($reference == '') {
                $this->flushCounterInstancesFromDB($path);
            }
            if (is_array($data) == true && isset($data['sample']) == true) {
                $this->parseCounterObject($path, $data, $reference);
            } else {
                if (is_array($data) == false) {
                    continue;
                }
                foreach ($data as $instance => $value) {
                    $this->parseCounterObject($instance, $value, $reference);
                }
            }
        }
    }

    public function flushCounterReferencesFromDb($reference)
    {
        if ($reference == '') {
            return;
        }

        $this->db->delete(
            'host_perf_counter',
            Filter::matchAll(
                Filter::expression('host_id', '=', $this->host_id),
                Filter::where('reference', $reference)
            )
        );
    }

    public function flushCounterInstancesFromDB($path)
    {
        $counter_name = $this->parseCounterName($path);
        $this->db->delete(
            'host_perf_counter',
            Filter::matchAll(
                Filter::expression('host_id', '=', $this->host_id),
                Filter::where('category', $counter_name['category']),
                Filter::where('counter', $counter_name['counter'])
            )
        );
    }

    protected function parseCounterName($path)
    {
        $category = '';
        $instance = '';
        $counter  = '';

        if (strpos($path, '(') == false) {
            $object = explode('\\', $path);

            if (empty($object[0])) {
                $category = $object[1];
                $counter  = $object[2];
            } else {
                $category = $object[0];
                $counter  = $object[1];
            }

            if (strpos($category, '(') !== false) {
                $object = explode('(', $category);
                $category = $object[0];
                $instance = str_replace(')', '', $object[1]);
            }
        } else {
            $category = substr($path, 1, strpos($path, '(') - 1);
            $bracketStart = strpos($path, '(') + 1;
            $bracketEnd = strpos($path, ')');
            $instance = substr($path, $bracketStart, $bracketEnd - $bracketStart);
            $counter = substr($path, $bracketEnd + 2, strlen($path) - $bracketEnd - 2);
        }

        return array(
            'category' => $category,
            'instance' => $instance,
            'counter'  => $counter
        );
    }

    public function parseCounterObject($path, $counter, $reference)
    {
        $queryColumns = array(
            'host_id' => $this->host_id
        );

        $counterHelpName = '';

        if ($reference == '') {
            $counter_name = $this->parseCounterName($path);
            $counterHelpName = $counter_name['category'];
            $queryColumns = array_merge(
                $queryColumns,
                array(
                    'category' => $counter_name['category'],
                    'instance' => $counter_name['instance'],
                    'counter' => $counter_name['counter'],
                )
            );
        } else {
            $queryColumns = array_merge(
                $queryColumns,
                array(
                    'counter' => $path,
                    'reference' => $reference
                )
            );
            $counterHelpName = $path;
        }

        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'raw_value', $counter['sample'], 'RawValue', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'base_value', $counter['sample'], 'BaseValue', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'system_frequency', $counter['sample'], 'SystemFrequency', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'counter_frequency', $counter['sample'], 'CounterFrequency', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'counter_timestamp', $counter['sample'], 'CounterTimeStamp', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'timestamp', $counter['sample'], 'TimeStamp', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'timestamp_100nsec', $counter['sample'], 'TimeStamp100nSec', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'value', $counter, 'value', 0);

        $this->db->insert(
            'host_perf_counter',
            $queryColumns
        );

        /*
         * This will allow us to write help informations for the counters inside the database
         * We only add data however, if we loaded the current available indexes before
         * This will speed up the entire loading
         */
        if ($this->counter_help == null) {
            return;
        }

        if ($this->counter_help->isIndexKnown($counterHelpName) == true) {
            return;
        }

        $queryColumns = array(
            'counter' => $counterHelpName,
        );
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'counter_type', $counter['sample'], 'CounterType', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'type', $counter, 'type');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'help', $counter, 'help');

        $this->db->insert(
            'host_perf_counter_help',
            $queryColumns
        );
    }
}