<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Html\Html;
use dipl\Translation\TranslationHelper;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Update;
use Icinga\Module\Windows\Object\Objects\Updates;

class InstalledUpdateInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $hostname;

    /**
     * @var Update
     */
    protected $update;

    protected $date;

    public function __construct($host, $update, $date)
    {
        $this->hostname = $host;

        $this->update = $update;

        $this->date = $date;

        $this->init();
    }

    protected function init()
    {
        $updates = new Updates($this->hostname);

        $this->update = $updates->loadUpdateHistoryFromDB(
            $this->update,
            $this->date
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
            $this->translate('Support Url') => Html::tag('a', [
                'href'   => $this->update->getSupportUrl(),
                'target' => '_blank',
                'title' => $this->translate('Jump to Microsoft Update knowledge base')
            ], $this->update->getSupportUrl()),
            $this->translate('Status') => $this->update->translateResultCode(),
            $this->translate('Installed On') => Tools::getInstance()->getDateFromPSOutput($this->update->getInstalledOn())
        ]);
    }
}
