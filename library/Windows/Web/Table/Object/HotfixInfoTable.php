<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Html\Html;
use dipl\Translation\TranslationHelper;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Object\Objects\Hotfix;
use Icinga\Module\Windows\Object\Objects\Updates;

class HotfixInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $hostname;

    /**
     * @var Hotfix
     */
    protected $hotfix;

    public function __construct($host, $hotfix)
    {
        $this->hostname = $host;

        $this->hotfix = $hotfix;

        $this->init();
    }

    protected function init()
    {
        $updates = new Updates($this->hostname);

        $this->hotfix = $updates->loadHotfixesFromDB(
            $this->hotfix
        );
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValuePairs([
            $this->translate('Hotfix Id') => $this->hotfix->getId(),
            $this->translate('Name') => $this->hotfix->getName() === null ? '-' : $this->hotfix->getName(),
            $this->translate('Description') => $this->hotfix->getDescription(),
            $this->translate('Status') => $this->hotfix->getStatus() === null ? '-' : $this->hotfix->getStatus(),
            $this->translate('Install Date') => $this->hotfix->getInstallDate(),
            $this->translate('Support Url') => Html::tag('a', [
                'href'   => $this->hotfix->getSupportUrl(),
                'target' => '_blank',
                'title' => $this->translate('Jump to Microsoft Update knowledge base')
            ], $this->hotfix->getSupportUrl()),
            $this->translate('Fix Comments') => $this->hotfix->getFixComment(),
            $this->translate('Service Pack') => $this->hotfix->getServicePack(),
            $this->translate('Installed By') => $this->hotfix->getInstalledBy()
        ]);
    }
}
