<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use gipfl\Translation\TranslationHelper;
use gipfl\IcingaWeb2\Link;
use gipfl\IcingaWeb2\Widget\NameValueTable;
use Icinga\Module\Windows\Object\Objects\Updates;

class UpdatesInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $hostname;

    /**
     * @var Updates
     */
    protected $updates;

    public function __construct($host)
    {
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->updates = new Updates($this->hostname);

        $this->updates->loadUpdateHistoryFromDB();

        $this->updates->loadHotfixesFromDB();

        $this->updates->loadPendingUpdatesFromDB();
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValuePairs([
            $this->translate('Pending Updates') => Link::create(Count($this->updates->getPendingUpdates()), 'windows/pendingupdates', [
                'host' => $this->hostname
            ], [
                'class' => 'action-link',
                'data-base-target' => '_next'
            ]),
            $this->translate('Installed Updates') => Link::create(Count($this->updates->getInstalledUpdates()), 'windows/updatehistory', [
                'host' => $this->hostname
            ], [
                'class' => 'action-link',
                'data-base-target' => '_next'
            ]),
            $this->translate('Installed Hotfixes') => Link::create(Count($this->updates->getHotfixes()), 'windows/hotfixes', [
                'host' => $this->hostname
            ], [
                'class' => 'action-link',
                'data-base-target' => '_next'
            ])
        ]);
    }
}
