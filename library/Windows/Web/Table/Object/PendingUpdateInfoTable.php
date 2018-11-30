<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Html\Html;
use dipl\Translation\TranslationHelper;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Update;
use Icinga\Module\Windows\Object\Objects\Updates;

class PendingUpdateInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $hostname;

    /**
     * @var Update
     */
    protected $update;

    public function __construct($host, $update)
    {
        $this->hostname = $host;

        $this->update = $update;

        $this->init();
    }

    protected function init()
    {
        $updates = new Updates($this->hostname);

        $this->update = $updates->loadPendingUpdatesFromDB(
            $this->update
        );
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValuePairs([
            $this->translate('Name') => $this->update->getName(),
            $this->translate('Description') => $this->update->getDescription(),
            $this->translate('Uninstall Notes') => $this->update->getUninstallNote(),
            $this->translate('Support Url') => Html::tag('a', [
                'href'   => $this->update->getSupportUrl(),
                'target' => '_blank',
                'title' => $this->translate('Jump to Microsoft Update knowledge base')
            ], $this->update->getSupportUrl()),
            $this->translate('Require Reboot') => $this->update->getRequireReboot() === true ? 'Yes' : 'No',
            $this->translate('Download Size') => round(Tools::getInstance()->convertBytesToMB($this->update->getDownloadSize()), 2) . ' MB',
            $this->translate('Downloaded') => $this->update->getDownloaded() === true ? 'Yes' : 'No',
            $this->translate('Superseded Update IDs') => $this->update->getSupersededIDs()
        ]);
    }
}
