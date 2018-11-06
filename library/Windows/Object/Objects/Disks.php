<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\PerfCounter\PerfCounter;

class Disks
{
    protected $hostname;

    protected $disk_hardware = array();

    protected $disk_drives = array();

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadFromDb()
    {
        $perfCounter = new PerfCounter($this->hostname);

        $diskHardware = new Hardware($this->hostname);
        $diskHardware->loadDiskHardware();

        foreach ($diskHardware->getDisks() as $id => $disk) {
            foreach($disk->getDrives() as $drive => $bool) {
                $perfCounter->loadReferenceCounterFromDB(
                    $drive,
                    array('value')
                );

                $this->disk_drives += array(
                    $drive => $disk
                );
            }

            $disk->setPerformanceCounterReference($perfCounter);

            $this->disk_hardware += array(
                $id => $disk
            );
        }
    }

    public function getDisks()
    {
        return $this->disk_hardware;
    }

    public function getDiskByHardware($hardware)
    {
        if (isset($this->disk_hardware[$hardware])) {
            return $this->disk_hardware[$hardware];
        }

        return (new Disk());
    }

    public function getDiskByDrive($drive)
    {
        $drive = str_replace(':', '', $drive);
        $drive = strtolower($drive);
        if (isset($this->disk_drives[$drive])) {
            return $this->disk_drives[$drive];
        }

        return (new Disk());
    }

    public function parseApiRequest($content)
    {
        $perfCounter = new PerfCounter($this->hostname);
        $perfCounter->loadPerformanceCounterHelpIndex();

        foreach ($content['output'] as $disk => $counter) {
            $perfCounter->flushCounterReferencesFromDb($disk);
            $perfCounter->parsePerfCounter($counter, $disk);
        }
    }
}