<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Link;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Hardware;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Memory;
use Icinga\Module\Windows\Web\Widget\MemoryUsage;

class MemoryInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Memory
     */
    protected $memory;

    /**
     * @var Hardware
     */
    protected $memory_hardware;

    /**
     * @var Hardware
     */
    protected $hardware;

    protected $hostname;

    protected $host_id;

    public function __construct($host)
    {
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->memory = new Memory($this->hostname);
        $this->hardware = new Hardware($this->hostname);
        $this->memory->loadFromDb();
        $this->hardware->loadMemoryHardware();
        $this->memory_hardware = $this->hardware->getMemory();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Memory details')), ''
        );

        $this->addNameValuePairs([
            $this->translate('Memory usage') => [
                Html::tag(
                    'div',
                    ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                    $this->createMemoryUsageBar()
                )
            ]
        ]);
        $this->addNameValueRow(
            'Installed modules',
            $this->hardware->getMemoryModuleCount()
        );
    }

    protected function createMemoryUsageBar()
    {
        $used = $this->memory->getUsedMemory();
        $total =  $this->memory->getTotalMemory();
        if ($total == 0) {
            $total = 1;
        }
        $usageBar = new MemoryUsage(
            $used,
            $total
        );

        return $usageBar;
    }
}
