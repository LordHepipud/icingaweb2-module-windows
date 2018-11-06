<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Processes extends BaseClass
{
    protected $host_id;

    protected $hostname;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * @var WindowsDB
     */
    protected $db;

    protected $counter = null;

    protected $processlist = null;

    protected $totalMetrics = array();

    public function __construct($hostname)
    {
        $this->dbHelper = DbHelper::getInstance();
        $this->db = WindowsDB::fromConfig();
        $this->hostname = $hostname;
        $this->host_id = $this->dbHelper->getHostIdByName($hostname);
    }

    protected function createProcessFromDB($proc)
    {
        if (!isset($this->processlist[$proc->proc_name])) {
            $this->processlist[$proc->proc_name] = array();
        }

        $process = new Process();

        $process->setName($proc->proc_name);

        $process->setCaption($proc->proc_name);

        $process->setProcessId($proc->proc_id);

        $process->setPriority($proc->proc_priority);

        $process->setThreadCount($proc->proc_threads);

        $process->setProcessorPercent($proc->proc_processor_percent);

        $process->setProcessProcessorTime($proc->proc_processor_time);

        $process->setPagefile($proc->proc_pagefile);

        $process->setCmdline($proc->proc_cmd);

        $process->setUsedMemory($proc->proc_used_memory);

        $process->setWorkingSetSize($proc->proc_required_memory);

        $this->processlist[$process->getName()] += array(
            $process->getProcessId() => $process
        );

        $this->updateTotalMetrics($process);
    }

    public function loadAllDB()
    {
        $query = $this->db->select()
            ->from(
                'host_process_list',
                array(
                    'proc_name',
                    'proc_id',
                    'proc_priority',
                    'proc_threads',
                    'proc_processor_percent',
                    'proc_processor_time',
                    'proc_pagefile',
                    'proc_used_memory',
                    'proc_required_memory',
                    'proc_cmd'
                )
            )->where(
                'host_id',
                $this->host_id
            )->order(
                'proc_required_memory',
                'DESC'
            );

        $allProcesses = $query->fetchAll();

        foreach ($allProcesses as $process) {
            $this->createProcessFromDB($process);
        }

        return $this->processlist;
    }

    public function loadSingleDB($proc)
    {
        $query = $this->db->select()
            ->from(
                'host_process_list',
                array(
                    'proc_name',
                    'proc_id',
                    'proc_priority',
                    'proc_threads',
                    'proc_processor_percent',
                    'proc_processor_time',
                    'proc_pagefile',
                    'proc_used_memory',
                    'proc_required_memory',
                    'proc_cmd'
                )
            )->where(
                'host_id',
                $this->host_id
            )->where(
                'proc_name',
                $proc
            )->order(
                'proc_required_memory',
                'DESC'
            );

        $allProcesses = $query->fetchAll();

        foreach ($allProcesses as $process) {
            $this->createProcessFromDB($process);
        }

        return (isset($this->processlist[$proc]) ? $this->processlist[$proc] : null);
    }

    public function updateTotalMetrics($process)
    {
        if (isset($this->totalMetrics[$process->getName()]['instances'])) {
            $this->totalMetrics[$process->getName()]['instances'] += 1;
        } else {
            $this->totalMetrics[$process->getName()]['instances'] = 1;
        }

        if (isset($this->totalMetrics[$process->getName()]['threads'])) {
            $this->totalMetrics[$process->getName()]['threads'] += $process->getThreadCount();
        } else {
            $this->totalMetrics[$process->getName()]['threads'] = $process->getThreadCount();
        }

        if (isset($this->totalMetrics[$process->getName()]['pagefile'])) {
            $this->totalMetrics[$process->getName()]['pagefile'] += $process->getPagefile();
        } else {
            $this->totalMetrics[$process->getName()]['pagefile'] = $process->getPagefile();
        }

        if (isset($this->totalMetrics[$process->getName()]['virualsize'])) {
            $this->totalMetrics[$process->getName()]['virualsize'] += $process->getVirtualSize();
        } else {
            $this->totalMetrics[$process->getName()]['virualsize'] = $process->getVirtualSize();
        }

        if (isset($this->totalMetrics[$process->getName()]['worksetsize'])) {
            $this->totalMetrics[$process->getName()]['worksetsize'] += $process->getWorkingSetSize();
        } else {
            $this->totalMetrics[$process->getName()]['worksetsize'] = $process->getWorkingSetSize();
        }

        if (isset($this->totalMetrics[$process->getName()]['usedmemory'])) {
            $this->totalMetrics[$process->getName()]['usedmemory'] += $process->getUsedMemory();
        } else {
            $this->totalMetrics[$process->getName()]['usedmemory'] = $process->getUsedMemory();
        }

        if (isset($this->totalMetrics[$process->getName()]['processorpercent'])) {
            $this->totalMetrics[$process->getName()]['processorpercent'] += $process->getProcessorPercent();
        } else {
            $this->totalMetrics[$process->getName()]['processorpercent'] = $process->getProcessorPercent();
        }

        if (isset($this->totalMetrics[$process->getName()]['processortime'])) {
            $this->totalMetrics[$process->getName()]['processortime'] += $process->getProcessProcessorTime(false);
        } else {
            $this->totalMetrics[$process->getName()]['processortime'] = $process->getProcessProcessorTime(false);
        }
    }

    public function getProcessList()
    {
        return $this->processlist;
    }

    public function getProcessCount($process)
    {
        if (isset($this->totalMetrics[$process]['instances'])) {
            return $this->totalMetrics[$process]['instances'];
        }
        return 0;
    }

    public function getProcessTotalThreads($process)
    {
        if (isset($this->totalMetrics[$process]['threads'])) {
            return $this->totalMetrics[$process]['threads'];
        }
        return 0;
    }

    public function getProcessTotalPagefile($process)
    {
        if (isset($this->totalMetrics[$process]['pagefile'])) {
            return $this->totalMetrics[$process]['pagefile'];
        }
        return 0;
    }

    public function getProcessTotalVirtualSize($process)
    {
        if (isset($this->totalMetrics[$process]['virualsize'])) {
            return $this->totalMetrics[$process]['virualsize'];
        }
        return 0;
    }

    public function getProcessTotalWorkingSetSize($process)
    {
        if (isset($this->totalMetrics[$process]['worksetsize'])) {
            return $this->totalMetrics[$process]['worksetsize'];
        }
        return 0;
    }

    public function getProcessTotalProcessorPercent($process)
    {
        if (isset($this->totalMetrics[$process]['processorpercent'])) {
            return $this->totalMetrics[$process]['processorpercent'];
        }
        return 0;
    }

    public function getProcessTotalUsedMemory($process)
    {
        if (isset($this->totalMetrics[$process]['usedmemory'])) {
            return $this->totalMetrics[$process]['usedmemory'];
        }
        return 0;
    }

    public function getProcessTotalProcessorTime($process)
    {
        if (isset($this->totalMetrics[$process]['processortime'])) {
            return ($this->totalMetrics[$process]['processortime'] / 10000000);
        }
        return 0;
    }

    public function getProcessListOfProcess($process)
    {
        if (isset($this->processlist[$process])) {
            return $this->processlist[$process];
        }
        return null;
    }

    public function parseApiRequest($content)
    {
        if (isset($content['output']) == false) {
            return;
        }

        if (isset($content['output']['FullList'])) {
            if (Count($content['output']['FullList']) != 0) {

                $this->db->delete(
                    'host_process_list',
                    Filter::expression('host_id', '=', $this->host_id)
                );

                foreach ($content['output']['FullList'] as $key => $proc) {
                    $this->addProcessToDb($proc);
                }

                return;
            }
        }

        if (isset($content['output']['Removed'])) {
            if (empty($content['output']['Removed']) == false) {

                foreach ($content['output']['Removed'] as $id) {

                    $this->db->delete(
                        'host_process_list',
                        Filter::matchAll(
                            Filter::expression('host_id', '=', $this->host_id),
                            Filter::where('proc_id', $id)
                        )
                    );
                }
            }
        }

        if (isset($content['output']['Added'])) {
            if (Count($content['output']['Added']) != 0) {
                foreach ($content['output']['Added'] as $key => $proc) {
                    $this->addProcessToDb($proc);
                }
            }
        }

        if (isset($content['output']['Modified'])) {
            if (Count($content['output']['Modified']) != 0) {
                foreach ($content['output']['Modified'] as $key => $proc) {
                    $this->updateProcess($proc);
                }
            }
        }
    }

    protected function addProcessToDb($proc)
    {
        if (is_array($proc) === false) {
            return;
        }

        $this->db->insert(
            'host_process_list',
            array(
                'host_id' => $this->host_id,
                'proc_name' => (isset($proc['Name']) && $proc['Name'] !== null) ? $proc['Name'] : '',
                'proc_id' => (isset($proc['ProcessId']) && $proc['ProcessId'] !== null) ? $proc['ProcessId'] : 0,
                'proc_priority' => (isset($proc['Priority']) && $proc['Priority'] !== null) ? $proc['Priority'] : 0,
                'proc_threads' => (isset($proc['ThreadCount']) && $proc['ThreadCount'] !== null) ? $proc['ThreadCount'] : 0,
                'proc_processor_percent' => (isset($proc['PercentProcessorTime']) && $proc['PercentProcessorTime'] !== null) ? $proc['PercentProcessorTime'] : 0,
                'proc_processor_time' => (
                    ((isset($proc['KernelModeTime']) && $proc['KernelModeTime'] !== null) ? $proc['KernelModeTime'] : 0) +
                    ((isset($proc['UserModeTime']) && $proc['UserModeTime'] !== null) ? $proc['UserModeTime'] : 0)
                ),
                'proc_pagefile' => (isset($proc['PageFileUsage']) && $proc['PageFileUsage'] !== null) ? $proc['PageFileUsage'] : 0,
                'proc_used_memory' => (isset($proc['WorkingSetPrivate']) && $proc['WorkingSetPrivate'] !== null) ? $proc['WorkingSetPrivate'] : 0,
                'proc_required_memory' => (isset($proc['WorkingSetSize']) && $proc['WorkingSetSize'] !== null) ? $proc['WorkingSetSize'] : 0,
                'proc_cmd' => (isset($proc['CommandLine']) && $proc['CommandLine'] !== null) ? $proc['CommandLine'] : ''
            )
        );
    }

    protected function updateProcess($proc)
    {
        if (isset($proc['ProcessId']) === false) {
            return;
        }

        $queryColumns = array();
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_name', $proc, 'Name');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_id', $proc, 'ProcessId');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_priority', $proc, 'Priority');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_processor_percent', $proc, 'PercentProcessorTime');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_pagefile', $proc, 'PageFileUsage');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_used_memory', $proc, 'WorkingSetPrivate');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_required_memory', $proc, 'WorkingSetSize');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'proc_cmd', $proc, 'CommandLine');

        $this->db->update(
            'host_process_list',
            $queryColumns,
            Filter::matchAll(
                Filter::expression('host_id', '=', $this->host_id),
                Filter::where('proc_id', $proc['ProcessId'])
            )
        );
    }
}