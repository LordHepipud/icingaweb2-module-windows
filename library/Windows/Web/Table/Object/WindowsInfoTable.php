<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Link;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Bios;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Windows;

class WindowsInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Windows
     */
    protected $windows;

    protected $service;

    protected $hostname;

    protected $host_id;

    public function __construct($host)
    {
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->windows = new Windows($this->hostname);
        $this->windows->loadFromDB();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Windows details')), ''
        );

        $this->addNameValuePairs([
            $this->translate('Operating System') => $this->windows->getCaption(),
            $this->translate('Version') => $this->windows->getVersion(),
            $this->translate('System Drive') => Link::create(
                $this->windows->getSystemDrive(),
                'windows/counters',
                [
                    'host'  => $this->hostname,
                    'reference' => (str_replace(':', '', $this->windows->getSystemDrive()))
                ],
                [
                    'class' => 'action-link',
                    'data-base-target' => '_next'
                ]
            )
        ]);
    }
}
