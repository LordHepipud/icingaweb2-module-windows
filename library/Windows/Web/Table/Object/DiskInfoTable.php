<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use gipfl\Translation\TranslationHelper;
use gipfl\IcingaWeb2\Link;
use ipl\Html\Html;
use gipfl\IcingaWeb2\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Disks;
use Icinga\Module\Windows\Web\Widget\DiskUsage;

class DiskInfoTable extends NameValueTable
{
    use TranslationHelper;

    /**
     * @var Memory
     */
    protected $disk;

    protected $hostname;

    protected $host_id;

    public function __construct($host)
    {
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->disk = new Disks($this->hostname);
        $this->disk->loadFromDb();
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $this->addNameValueRow(
            Html::tag('h3', $this->translate('Disk details')), ''
        );

        foreach ($this->disk->getDisks() as $drive => $disk) {
            $this->addNameValueRow(
                Html::tag('h4', $disk->getModel()), ''
            );
            foreach ($disk->getDrives() as $_drive => $bool) {
                $this->addNameValueRow(
                    $this->createDiskLink($_drive),
                    [
                        Html::tag(
                            'div',
                            ['style' => 'width: 50%; display:inline-block; margin-right: 1em;'],
                            $this->createDiskUsageBar($disk, $_drive)
                        )
                    ]
                );
            }
        }
    }

    protected function createDiskLink($drive)
    {
        if (DbHelper::getInstance()->hasReferences($drive, $this->hostname) == false) {
            return $drive;
        }

        return Link::create(
            (strtoupper($drive) . ':'),
            'windows/counters',
            [
                'host'      => $this->hostname,
                'reference' => strtoupper($drive)
            ],
            [
                'class' => 'action-link',
                'data-base-target' => '_next'
            ]
        );
    }

    protected function createDiskUsageBar($disk, $drive)
    {
        $size = $disk->getSize();
        $used = 0;

        if ($disk->getFreePercent($drive) == 0) {
            $used = $size;
        } else {
            $size = ($disk->getFreeMb($drive) / $disk->getFreePercent($drive)) * 100;
            $used = $size - $disk->getFreeMb($drive);
        }

        $size = Tools::getInstance()->convertMBToBytes($size);
        $used = Tools::getInstance()->convertMBToBytes($used);

        $usageBar = new DiskUsage(
            $used,
            $size
        );

        return $usageBar;
    }
}
