<?php

namespace Icinga\Module\Windows\Web\Table;

use gipfl\IcingaWeb2\Link;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Tools;

class InstalledUpdatesTable extends BaseTable
{
    protected $hostname;

    protected $searchColumns = [
        'name',
        'result',
        'installed_on'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('result', $this->translate('Status'), [

            ])->setRenderer(function($row) {
                return Tools::getInstance()->getUpdateResultCodeIcon($row->result);
            }),
            $this->createColumn('name', $this->translate('Update'), [
            ])->setRenderer(function ($row) {
                return Link::create($row->name, 'windows/installedupdate', [
                    'name' => $row->name,
                    'date' => $row->installed_on,
                    'host' => $this->hostname
                ], [
                    'icon' => 'clock',
                    'class' => 'action-link'
                ]);
            }),
            $this->createColumn('installed_on', $this->translate('Install Date'), [
            ])->setRenderer(function($row) {
                return Tools::getInstance()->getDateFromPSOutput($row->installed_on);
            })
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'name',
            'installed_on',
            'result'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_update_history', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id);

        return $db;
    }
}