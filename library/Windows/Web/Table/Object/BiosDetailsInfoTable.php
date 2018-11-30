<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Bios;
use Icinga\Module\Windows\Helper\Tools;

class BiosDetailsInfoTable extends NameValueTable
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
        foreach ($this->bios->getBiosData() as $bios){
            $this->addNameValueRow(
                Html::tag('h3', ($bios->getManufacturer() . ' (' . $bios->getSerialNumber() .')')), ''
            );
            $this->addNameValuePairs([
                $this->translate('Status') => $bios->getStatus(),
                $this->translate('Manufacturer') => $bios->getManufacturer(),
                $this->translate('Service Tag') => $bios->getSerialNumber(),
                $this->translate('Name') => $bios->getName(),
                $this->translate('Version') => $bios->getVersion(),
                $this->translate('Caption') => $bios->getCaption(),
                $this->translate('Description') => $bios->getDescription(),
                $this->translate('Available Languages') => $bios->getAvailableLanguages(),
                $this->translate('Current Language') => $bios->getCurrentLanguage(),
                $this->translate('Installable Languages') => $bios->getInstallableLanguages(),
                $this->translate('Characteristics') => $bios->getBiosCharacteristics(),
                $this->translate('Release Date') => Tools::getInstance()->getDateFromPSOutput($bios->getReleaseDate()),
                $this->translate('Bios Version') => $bios->getBiosVersion(),
                $this->translate('Embedded Controller Version') =>
                    $bios->getEmbeddedControllerMajorVersion() . '.' . $bios->getEmbeddedControllerMinorVersion(),
                $this->translate('System Bios Version') =>
                    $bios->getSystemBiosMajorVersion() . '.' . $bios->getSystemBiosMinorVersion(),
                $this->translate('SMBios Version') => $bios->getSmbiosVersion(),
                $this->translate('SMBios Build') =>
                    $bios->getSmbiosMajorVersion() . '.' . $bios->getSmbiosMinorVersion(),
                $this->translate('Is Primary') => $bios->getPrimaryBios() == 1 ? 'Yes' : 'No',
                $this->translate('SMBios Present') => $bios->getSmbiosPresent() == 1 ? 'Yes' : 'No',
                $this->translate('Software Element Id') => $bios->getSoftwareElementId(),
                $this->translate('Software Element State') => $bios->getSoftwareElementState(),
            ]);
        }
    }
}
