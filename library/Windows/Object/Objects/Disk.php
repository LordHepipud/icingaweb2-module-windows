<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\PerfCounter\PerfCounter;
use Icinga\Module\Windows\PerfCounter\PerfCounterObject;

class Disk
{
    protected $diskId;

    protected $size;

    protected $serialNumber;

    protected $partitions;

    protected $model;

    protected $freePercent;

    protected $freeMb;

    protected $drives = array();

    /**
     * @var PerfCounter
     */
    protected $references = null;

    /**
     * @return mixed
     */
    public function getDiskId()
    {
        return $this->diskId;
    }

    /**
     * @param mixed $diskId
     */
    public function setDiskId($diskId)
    {
        $this->diskId = $diskId;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return mixed
     */
    public function getPartitions()
    {
        return $this->partitions;
    }

    /**
     * @param mixed $partitions
     */
    public function setPartitions($partitions)
    {
        $this->partitions = $partitions;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setPerformanceCounterReference($reference)
    {
        $this->references = $reference;
    }

    protected function getReference($reference)
    {
        if ($this->references == null) {
            return (new PerfCounterObject(null));
        }

        return $this->references->getReference($reference);
    }

    /**
     * @return mixed
     */
    public function getFreePercent($drive)
    {
        return $this->getReference($drive . '\\% Free Space')->getValue();
    }

    /**
     * @return mixed
     */
    public function getFreeMb($drive)
    {
        return $this->getReference($drive . '\\Free Megabytes')->getValue();
    }

    public function getUsedMb()
    {
        return (
            Tools::getInstance()->convertBytesToMB($this->size) - $this->freeMb
        );
    }

    public function setDrives($db_drives)
    {
        if($db_drives == '') {
            return;
        }
        $tmp_drives = str_replace(':', '', $db_drives);
        $tmp_drives = explode(',', $tmp_drives);
        if (empty($tmp_drives) || count($tmp_drives) == 0) {
            return;
        }

        foreach($tmp_drives as $drive) {
            $this->drives += array(
                strtolower($drive) => true
            );
        }
    }

    public function getDrives()
    {
        return $this->drives;
    }

    public function hasDrive($drive)
    {
        if(Empty($this->drives) == true) {
            return false;
        }

        $drive = str_replace(':', '', $drive);
        return array_key_exists(strtolower($drive), $this->drives);
    }
}