<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Link;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Bios;

class BiosInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Bios
     */
    protected $bios;

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
        $this->bios = new Bios($this->hostname);
        $this->bios->loadAllFromDB();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Bios details')), ''
        );

        foreach ($this->bios->getBiosData() as $bios){
            $this->addNameValuePairs([
                $this->translate('Manufacturer') => $bios->getManufacturer(),
                $this->translate('Service Tag') => $bios->getSerialNumber(),
                $this->translate('Details') => Link::create(
                    'Bios',
                    'windows/bios',
                    [
                        'host'      => $this->hostname,
                        'servicetag' => $bios->getSerialNumber()
                    ],
                    [
                        'class' => 'action-link',
                        'data-base-target' => '_next'
                    ]
                )
            ]);
        }
    }
}
