<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Html\Link;
use dipl\Html\Html;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Cpu;
use Icinga\Module\Windows\Object\Objects\CpuCore;
use Icinga\Module\Windows\Object\Objects\CpuHCore;
use Icinga\Module\Windows\Object\Objects\Hardware;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Web\Widget\CpuUsage;

class ProcessorDetailsInfoTable extends NameValueTable
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
        $this->addNameValuePairs([
            $this->translate('Total Usage') => [
                Html::tag(
                    'div',
                    ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                    $this->createCpuUsageBar()
                )
            ]
        ]);

        for ($i = 0; $i < $this->hardware->getTotalThreads(); $i++) {
            $this->addNameValuePairs([
            ($this->translate('Thread #') . $i) =>
                Html::tag(
                    'div',
                    ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                    $this->createCpuUsageBar($i)
                )
            ]);
        }

        foreach ($this->hardware->getCPU() as $core) {
            $this->addNameValueRow(
                Html::tag('h3', $this->translate('Physical Core') . ' ' . $core->getId()), ''
            );

            $this->addNameValuePairs([
                $this->translate('Name') => $core->getName(),
                $this->translate('Description') => $core->getDescription(),
                $this->translate('Cores') => $core->getCores(),
                $this->translate('Threads') => $core->getThreads(),
                $this->translate('Partnumber') => $core->getPartNumber(),
                $this->translate('Revision') => $core->getRevision(),
                $this->translate('Serial Number') => $core->getSerialNumber(),
                $this->translate('Processor Id') => $core->getProcessorId(),
                $this->translate('Clock Speed') =>
                    $core->getCurrentClockSpeed() . '//' . $core->getMaxClockSpeed(),
                $this->translate('Voltage') => $core->getCurrentVoltage()
            ]);
        }
    }

    protected function createCpuUsageBar($core = null)
    {
        if ($core === null) {
            $usageBar = new CpuUsage(
                round($this->cpu->getTotalCore()->getValue(), 2),
                100
            );
        } else {
            $usageBar = new CpuUsage(
                round($this->cpu->getCoreById($core)->getValue(), 2),
                100
            );
        }

        $usageBar->setFormatter(
            function ($value) { return ($value . '%'); }
        );

        return $usageBar;
    }
}
