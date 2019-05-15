<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use gipfl\Translation\TranslationHelper;
use gipfl\IcingaWeb2\Link;
use ipl\Html\Html;
use gipfl\IcingaWeb2\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Network;
use Icinga\Module\Windows\Web\Widget\InterfaceUsage;

class NetworkInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Network
     */
    protected $network;

    protected $hostname;

    protected $host_id;

    public function __construct($host)
    {
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->network = new Network($this->hostname);
        $this->network->loadPhysicalFromDb();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Network details')), ''
        );

        foreach ($this->network->getPhysicalInterfaces() as $name => $interface) {
            $this->addNameValueRow(
               $this->createInterfaceCounterLink($name),
                Html::tag(
                    'div',
                    ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                    $this->createInterfaceUsageBar($interface)
                )
            );
        }
    }

    protected function createInterfaceCounterLink($name)
    {
        if (DbHelper::getInstance()->hasReferences($name, $this->hostname) == false) {
            return $name;
        }

        return Link::create(
            $name,
            'windows/counters',
            [
                'host'      => $this->hostname,
                'reference' => $name
            ],
            [
                'class' => 'action-link',
                'data-base-target' => '_next'
            ]
        );
    }

    protected function createInterfaceUsageBar($interface)
    {
        $size = $interface->getSpeed();

        if ($size == 0) {
            $size = $interface->getReference(
                $interface->getName() . '\\' . 'Current Bandwidth'
            )->getValue();
        }
        $used = $interface->getReference(
            $interface->getName() . '\\' . 'Bytes Total/sec'
        )->getValue();

        if ($size == 0) {
            $size = 1;
        }

        $usageBar = new InterfaceUsage(
            $used,
            $size
        );

        return $usageBar;
    }
}
