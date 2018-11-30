<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Link;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Cpu;
use Icinga\Module\Windows\Object\Objects\Hardware;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Web\Widget\CpuUsage;

class ProcessorInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Cpu
     */
    protected $cpu;

    /**
     * @var Hardware
     */
    protected $cpu_hardware;

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
        $this->cpu = new Cpu($this->hostname);
        $this->hardware = new Hardware($this->hostname);
        $this->cpu->loadFromDb();
        $this->hardware->loadCPUHardware();
        $this->cpu_hardware = $this->hardware->getCPU();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Processor details')), ''
        );

        $this->addNameValuePairs([
            $this->translate('CPU usage') => [
                Html::tag(
                    'div',
                    ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                    $this->createCpuUsageBar()
                )
            ]
        ]);
        $this->addNameValueRow(
            'Maximum Threads',
            Link::create(
                $this->hardware->getTotalThreads(),
                'windows/processor',
                [
                    'host'      => $this->hostname
                ],
                [
                    'class' => 'action-link',
                    'data-base-target' => '_next'
                ]
            )
        );
    }

    protected function createCpuUsageBar()
    {
        $usageBar = new CpuUsage(
            round($this->cpu->getTotalCore()->getValue(), 2),
            100
        );

        $usageBar->setFormatter(
            function ($value) { return ($value . '%'); }
        );

        return $usageBar;
    }
}
