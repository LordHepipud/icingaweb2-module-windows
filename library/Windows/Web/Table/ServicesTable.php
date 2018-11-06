<?php

namespace Icinga\Module\Windows\Web\Table;

use dipl\Html\Link;
use Icinga\Module\Windows\Helper\DbHelper;

class ServicesTable extends BaseTable
{
    protected $hostname;

    protected $searchColumns = [
        'display_name',
        'service_name',
        'status'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('display_name', $this->translate('Name'), [
                'display_name',
                'service_name'
            ])->setRenderer(function ($row) {
                return Link::create($row->display_name, 'windows/service', [
                    'service' => $row->service_name,
                    'host' => $this->hostname
                ], [
                    'icon' => 'chart-area',
                    'class' => 'action-link'
                ]);
            }),
            $this->createColumn('service_name', $this->translate('Service Name')),
            $this->createColumn('status', $this->translate('Status'), [
                'display_name',
                'service_name',
                'status'
            ])->setRenderer(function ($row) {
                switch($row->status)
                {
                    case 1:
                        return 'Stopped';
                    case 2:
                        return 'Starting';
                    case 3:
                        return 'Stopping';
                    case 4:
                        return 'Running';
                    case 5:
                        return 'Continue Pending';
                    case 6:
                        return 'Pause Pending';
                    case 7:
                        return 'Paused';
                }
                return $row->status;
            }),
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'display_name',
            'service_name',
            'status'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_service_list', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id);
        return $db;
    }
}