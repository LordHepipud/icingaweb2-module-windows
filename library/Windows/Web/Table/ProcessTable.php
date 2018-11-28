<?php

namespace Icinga\Module\Windows\Web\Table;

use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Tools;

class ProcessTable extends BaseTable
{
    protected $hostname;

    protected $process;

    protected $searchColumns = [
        'proc_id',
        'proc_cmd'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    public function setProcess($process)
    {
        $this->process = $process;
    }

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('proc_name', $this->translate('Binary')),
            $this->createColumn('proc_id', $this->translate('Id')),
            $this->createColumn('proc_priority', $this->translate('Priority')),
            $this->createColumn('proc_processor_percent', $this->translate('Processor in %')),
            $this->createColumn('proc_threads', $this->translate('Threads')),
            $this->createColumn('proc_used_memory', $this->translate('Used Memory'), [
                'proc_used_memory',
                'proc_used_memory'
            ])->setRenderer(function($row) {
                return sprintf('%s MB',
                    round(Tools::getInstance()->convertBytesToMB($row->proc_used_memory), 2)
                );
            }),
            $this->createColumn('proc_required_memory', $this->translate('Required Memory'), [
                'proc_required_memory'
            ])->setRenderer(function($row) {
                return sprintf('%s MB',
                    round(Tools::getInstance()->convertBytesToMB($row->proc_required_memory), 2)
                );
            }),
            $this->createColumn('proc_cmd', $this->translate('Command Line'))
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'proc_name',
            'proc_id',
            'proc_priority',
            'proc_threads',
            'proc_processor_percent',
            'proc_required_memory',
            'proc_cmd',
            'proc_used_memory'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_process_list', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id)
            ->where('proc_name = ?', $this->process);

        return $db;
    }
}