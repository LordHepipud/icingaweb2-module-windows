<?php

namespace Icinga\Module\Windows\Web\Table;

use gipfl\IcingaWeb2\Link;
use Icinga\Module\Windows\Helper\DbHelper;

class HotfixTable extends BaseTable
{
    protected $hostname;

    protected $searchColumns = [
        'id',
        'description',
        'install_date'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('id', $this->translate('ID'), [
            ])->setRenderer(function ($row) {
                return Link::create($row->id, 'windows/hotfix', [
                    'id'    => $row->id,
                    'host'  => $this->hostname
                ], [
                    'icon'  => 'history',
                    'class' => 'action-link'
                ]);
            }),
            $this->createColumn('description', $this->translate('Type')),
            $this->createColumn('install_date', $this->translate('Installed On'))
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'id',
            'description',
            'install_date'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_hotfix_history', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id);
        return $db;
    }
}