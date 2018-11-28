<?php

namespace Icinga\Module\Windows\Web\Table;

use dipl\Html\Link;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Processes;

class ProcessesTable extends BaseTable
{
    protected $hostname;

    /**
     * @var Processes
     */
    protected $processes;

    protected $searchColumns = [
        'proc_name'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function initialize()
    {
        $this->processes = new Processes($this->hostname);
        $this->processes->loadAllDB();

        $this->addAvailableColumns([
            $this->createColumn('proc_name', $this->translate('Process'), [
                'proc_name'
            ])->setRenderer(function ($row) {
                return Link::create($row->proc_name, 'windows/process', [
                    'process' => $row->proc_name,
                    'host' => $this->hostname
                ], [
                    'icon' => 'tasks',
                    'class' => 'action-link'
                ]);
            }),
            $this->createColumn('proc_id', $this->translate('Instances'), [
                'proc_name',
                'proc_id'
            ])->setRenderer(function ($row) {
                return $this->processes->getProcessCount($row->proc_name);
            }),
            $this->createColumn('proc_threads', $this->translate('Threads'), [
                'proc_name',
                'proc_threads'
            ])->setRenderer(function ($row) {
                return $this->processes->getProcessTotalThreads($row->proc_name);
            }),
            $this->createColumn('proc_processor_percent', $this->translate('Processor in %'), [
                'proc_name',
                'proc_processor_percent'
            ])->setRenderer(function ($row) {
                return $this->processes->getProcessTotalProcessorPercent($row->proc_name);
            }),
            $this->createColumn('proc_used_memory', $this->translate('Memory usage'), [
                'proc_name',
                'proc_used_memory'
            ])->setRenderer(function ($row) {
                return sprintf(
                    '%s MB',
                    round(Tools::getInstance()->convertBytesToMB(
                        $this->processes->getProcessTotalUsedMemory(
                            $row->proc_name
                        )
                    ), 2)
                );
            })
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'proc_name',
            'proc_threads',
            'proc_processor_percent',
            'proc_used_memory',
            'proc_id'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_process_list', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id)
            ->group('proc_name');
        return $db;
    }
}