<?php

namespace Icinga\Module\Windows\Web\Table;

use gipfl\IcingaWeb2\Link;
use Icinga\Module\Windows\Helper\DbHelper;

class PendingUpdatesTable extends BaseTable
{
    protected $hostname;

    protected $searchColumns = [
        'name',
        'description'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('name', $this->translate('Name'), [
            ])->setRenderer(function ($row) {
                return Link::create($row->name, 'windows/pendingupdate', [
                    'name' => $row->name,
                    'host' => $this->hostname
                ], [
                    'icon' => 'clock',
                    'class' => 'action-link'
                ]);
            }),
            $this->createColumn('kbarticles', $this->translate('KB Article')),
            $this->createColumn('downloaded', $this->translate('Downloaded'), [
            ])->setRenderer(function($row) {
                if ($row->downloaded == 1) {
                    return 'Yes';
                }
                return 'No';
            }),
            $this->createColumn('require_reboot', $this->translate('Require Reboot'), [
            ])->setRenderer(function($row) {
                if ($row->require_reboot == 1) {
                    return 'Yes';
                }
                return 'No';
            }),
        ]);
    }

    public function getDefaultColumnNames()
    {
        return [
            'name',
            'description',
            'kbarticles',
            'downloaded',
            'require_reboot'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_pending_updates', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id);
        return $db;
    }
}