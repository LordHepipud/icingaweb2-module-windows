<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Properties;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Hardware extends BaseClass
{
    protected $host_id;

    protected $hostname;

    protected $db;

    protected $cpu_cores = array();

    protected $ram_modules = array();

    protected $disks = array();

    protected $max_threads = 0;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
        $this->db = WindowsDB::fromConfig();
        $this->host_id = DbHelper::getInstance()->getHostIdByName(
            $this->hostname
        );
    }

    public function loadAllFromDB()
    {
        $this->loadCPUHardware();
        $this->loadMemoryHardware();
        $this->loadDiskHardware();
    }

    public function loadCPUHardware()
    {
        $query = $this->db->select()
            ->from(
                'host_hardware_cpu',
                array(
                    'cpu_id',
                    'architecture',
                    'description',
                    'name',
                    'number_logical_processors',
                    'number_cores',
                    'processor_id',
                    'current_clock_speed',
                    'max_clock_speed'
                )
            )->where(
                'host_id',
                $this->host_id
            );

        $result = $query->fetchAll();

        foreach ($result as $cpu) {
            $core = new CpuHCore();

            if (property_exists($cpu, 'cpu_id')) {
                $core->setId($cpu->cpu_id);
            }

            /*if (property_exists($cpu, 'architecture')) {
                $core->setArchitecture($cpu->architecture);
            }*/

            if (property_exists($cpu, 'description')) {
                $core->setDescription($cpu->description);
            }

            if (property_exists($cpu, 'name')) {
                $core->setName($cpu->name);
            }

            if (property_exists($cpu, 'number_cores')) {
                $core->setCores($cpu->number_cores);
            }

            if (property_exists($cpu, 'number_logical_processors')) {
                $core->setThreads($cpu->number_logical_processors);
                $this->max_threads += $cpu->number_logical_processors;
            }

            if (property_exists($cpu, 'processor_id')) {
                $core->setProcessorId($cpu->processor_id);
            }

            if (property_exists($cpu, 'current_clock_speed')) {
                $core->setCurrentClockSpeed($cpu->current_clock_speed);
            }

            if (property_exists($cpu, 'max_clock_speed')) {
                $core->setMaxClockSpeed($cpu->max_clock_speed);
            }

            array_push(
                $this->cpu_cores,
                $core
            );
        }
    }

    public function loadMemoryHardware()
    {
        $query = $this->db->select()
            ->from(
                'host_hardware_memory',
                array(
                    'capacity',
                    'part_number',
                    'serial',
                    'manufacturer',
                    'location'
                )
            )->where(
                'host_id',
                $this->host_id
            );

        $result = $query->fetchAll();

        foreach ($result as $memory) {
            $ram = new RamInfo();

            if (property_exists($memory, 'capacity')) {
                $ram->setCapacity($memory->capacity);
            }

            if (property_exists($memory, 'part_number')) {
                $ram->setPartNumber($memory->part_number);
            }

            if (property_exists($memory, 'serial')) {
                $ram->setSerialNumber($memory->serial);
            }

            if (property_exists($memory, 'manufacturer')) {
                $ram->setManufacturer($memory->manufacturer);
            }

            if (property_exists($memory, 'location')) {
                $ram->setLocation($memory->location);
            }

            array_push(
                $this->ram_modules,
                $ram
            );
        }
    }

    public function loadDiskHardware()
    {
        $query = $this->db->select()
            ->from(
                'host_hardware_disk',
                array(
                    'disk_id',
                    'serial_number',
                    'partitions',
                    'model',
                    'size',
                    'drive_reference'
                )
            )->where(
                'host_id',
                $this->host_id
            );

        $result = $query->fetchAll();

        foreach ($result as $_disk) {
            $disk = new Disk();

            if (property_exists($_disk, 'disk_id')) {
                $disk->setDiskId($_disk->disk_id);
            }

            if (property_exists($_disk, 'serial_number')) {
                $disk->setSerialNumber($_disk->serial_number);
            }

            if (property_exists($_disk, 'partitions')) {
                $disk->setPartitions($_disk->partitions);
            }

            if (property_exists($_disk, 'model')) {
                $disk->setModel($_disk->model);
            }

            if (property_exists($_disk, 'size')) {
                $disk->setSize($_disk->size);
            }

            if (property_exists($_disk, 'drive_reference')) {
                $disk->setDrives($_disk->drive_reference);
            }

            array_push(
                $this->disks,
                 $disk
            );
        }

        return $this;
    }

    public function getCPU()
    {
        return $this->cpu_cores;
    }

    public function getMemory()
    {
        return $this->ram_modules;
    }

    public function getDisks()
    {
        return $this->disks;
    }

    public function getTotalThreads()
    {
        return $this->max_threads;
    }

    public function getMemoryModuleCount()
    {
        return count($this->ram_modules);
    }

    public function parseApiRequest($content)
    {
        if (isset($content['output']) == false) {
            return;
        }

        if (isset($content['output']['cpu']) !== false) {
            $this->parseCPUHardware($content);
        }

        if (isset($content['output']['memory']) !== false) {
            $this->parseMemoryHardware($content);
        }

        if (isset($content['output']['disks']) !== false) {
            $this->parseDiskHardware($content);
        }
    }

    protected function parseCPUHardware($content)
    {
        // Handle pending updates

        $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
        $this->db->delete(
            'host_hardware_cpu',
            $deleteFilter
        );

        foreach ($content['output']['cpu'] as $index => $cpu) {

            $virtualisation_enabled = 0;

            if (isset($cpu['SupersededUpdateIDs']) && $cpu['VirtualizationFirmwareEnabled'] !== null) {
                if ($cpu['VirtualizationFirmwareEnabled'] == true) {
                    $virtualisation_enabled = 1;
                }
            }

            $this->db->insert(
                'host_hardware_cpu',
                array(
                    'host_id'   => $this->host_id,
                    'cpu_id' => (isset($cpu['DeviceID']) && $cpu['DeviceID'] !== null) ? str_replace('CPU', '', $cpu['DeviceID']) : '',
                    'architecture' => (isset($cpu['Architecture']) && $cpu['Architecture'] !== null) ? $cpu['Architecture'] : 0,
                    'l2_cache_size' => (isset($cpu['L2CacheSize']) && $cpu['L2CacheSize'] !== null) ? $cpu['L2CacheSize'] : 0,
                    'description' => (isset($cpu['Description']) && $cpu['Description'] !== null) ? $cpu['Description'] : '',
                    'processor_id' => (isset($cpu['ProcessorId']) && $cpu['ProcessorId'] !== null) ? $cpu['ProcessorId'] : '',
                    'current_clock_speed' => (isset($cpu['CurrentClockSpeed']) && $cpu['CurrentClockSpeed'] !== null) ? $cpu['CurrentClockSpeed'] : 0,
                    'max_clock_speed' => (isset($cpu['MaxClockSpeed']) && $cpu['MaxClockSpeed'] !== null) ? $cpu['MaxClockSpeed'] : 0,
                    'virtualization_enabled' => $virtualisation_enabled,
                    'number_cores' => (isset($cpu['NumberOfCores']) && $cpu['NumberOfCores'] !== null) ? $cpu['NumberOfCores'] : 0,
                    'number_logical_processors' => (isset($cpu['NumberOfLogicalProcessors']) && $cpu['NumberOfLogicalProcessors'] !== null) ? $cpu['NumberOfLogicalProcessors'] : 0,
                    'thread_count' => (isset($cpu['ThreadCount']) && $cpu['ThreadCount'] !== null) ? $cpu['ThreadCount'] : 1,
                    'manufacturer' => (isset($cpu['Manufacturer']) && $cpu['Manufacturer'] !== null) ? $cpu['Manufacturer'] : '',
                    'caption' => (isset($cpu['Caption']) && $cpu['Caption'] !== null) ? $cpu['Caption'] : '',
                    'revision' => (isset($cpu['Revision']) && $cpu['Revision'] !== null) ? $cpu['Revision'] : 0,
                    'address_width' => (isset($cpu['AddressWidth']) && $cpu['AddressWidth'] !== null) ? $cpu['AddressWidth'] : 0,
                    'family' => (isset($cpu['Family']) && $cpu['Family'] !== null) ? $cpu['Family'] : 0,
                    'level' => (isset($cpu['Level']) && $cpu['Level'] !== null) ? $cpu['Level'] : 0,
                    'cpu_status' => (isset($cpu['CpuStatus']) && $cpu['CpuStatus'] !== null) ? $cpu['CpuStatus'] : 0,
                    'name' => (isset($cpu['Name']) && $cpu['Name'] !== null) ? $cpu['Name'] : ''
                )
            );
        }
    }

    protected function parseMemoryHardware($content)
    {
        // Handle pending updates

        $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
        $this->db->delete(
            'host_hardware_memory',
            $deleteFilter
        );

        foreach ($content['output']['memory'] as $index => $memory) {
            if (! is_array($memory)) {
                continue;
            }

            $this->db->insert(
                'host_hardware_memory',
                array(
                    'host_id'   => $this->host_id,
                    'bank_label' => (isset($memory['bank_label']) && $memory['bank_label'] !== null) ? $memory['bank_label'] : '',
                    'manufacturer' => (isset($memory['manufacturer']) && $memory['manufacturer'] !== null) ? $memory['manufacturer'] : '',
                    'capacity' => (isset($memory['capacity']) && $memory['capacity'] !== null) ? $memory['capacity'] : 0,
                    'memory_tye' => (isset($memory['memory_tye']) && $memory['memory_tye'] !== null) ? $memory['memory_tye'] : 0,
                    'total_width' => (isset($memory['total_width']) && $memory['total_width'] !== null) ? $memory['total_width'] : 0,
                    'clock_speed' => (isset($memory['configured_clock_speed']) && $memory['configured_clock_speed'] !== null) ? $memory['configured_clock_speed'] : 0,
                    'description' => (isset($memory['desc']) && $memory['desc'] !== null) ? $memory['desc'] : '',
                    'tag' => (isset($memory['tag']) && $memory['tag'] !== null) ? $memory['tag'] : '',
                    'location' => (isset($memory['device_locator']) && $memory['device_locator'] !== null) ? $memory['device_locator'] : '',
                    'caption' => (isset($memory['caption']) && $memory['caption'] !== null) ? $memory['caption'] : '',
                    'serial' => (isset($memory['serial_number']) && $memory['serial_number'] !== null) ? $memory['serial_number'] : '',
                    'part_number' => (isset($memory['part_number']) && $memory['part_number'] !== null) ? $memory['part_number'] : '',
                    'min_voltage' => (isset($memory['min_voltage']) && $memory['min_voltage'] !== null) ? $memory['min_voltage'] : 0,
                    'max_voltage' => (isset($memory['max_voltage']) && $memory['max_voltage'] !== null) ? $memory['max_voltage'] : 0,
                    'configured_voltage' => (isset($memory['configured_voltage']) && $memory['configured_voltage'] !== null) ? $memory['configured_voltage'] : 0,
                )
            );
        }
    }

    protected function parseDiskHardware($content)
    {
        // Handle pending updates
        $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
        $this->db->delete(
            'host_hardware_disk',
            $deleteFilter
        );

        foreach ($content['output']['disks'] as $index => $disk) {
            $this->db->insert(
                'host_hardware_disk',
                array(
                    'host_id'  => $this->host_id,
                    'disk_id' => (isset($disk['DeviceID']) && $disk['DeviceID'] !== null) ? $disk['DeviceID'] : '',
                    'description' => (isset($disk['Description']) && $disk['Description'] !== null) ? $disk['Description'] : '',
                    'firmware_revision' => (isset($disk['FirmwareRevision']) && $disk['FirmwareRevision'] !== null) ? $disk['FirmwareRevision'] : '',
                    'drive_reference' => (isset($disk['DriveReference']) && $disk['DriveReference'] !== null) ? implode(',', $disk['DriveReference']) : '',
                    'caption' => (isset($disk['Caption']) && $disk['Caption'] !== null) ? $disk['Caption'] : '',
                    'total_heads' => (isset($disk['TotalHeads']) && $disk['TotalHeads'] !== null) ? $disk['TotalHeads'] : 0,
                    'model' => (isset($disk['Model']) && $disk['Model'] !== null) ? $disk['Model'] : '',
                    'size' => (isset($disk['Size']) && $disk['Size'] !== null) ? $disk['Size'] : 0,
                    'partitions' => (isset($disk['Partitions']) && $disk['Partitions'] !== null) ? $disk['Partitions'] : 0,
                    'serial_number' => (isset($disk['SerialNumber']) && $disk['SerialNumber'] !== null) ? $disk['SerialNumber'] : '',
                    'scsi_target_id' => (isset($disk['SCSITargetId']) && $disk['SCSITargetId'] !== null) ? $disk['SCSITargetId'] : 0,
                    'scsi_logical_id' => (isset($disk['SCSILogicalUnit']) && $disk['SCSILogicalUnit'] !== null) ? $disk['SCSILogicalUnit'] : 0,
                    'scsi_port' => (isset($disk['SCSIPort']) && $disk['SCSIPort'] !== null) ? $disk['SCSIPort'] : 0,
                    'media_type' => (isset($disk['MediaType']) && $disk['MediaType'] !== null) ? $disk['MediaType'] : '',
                    'bytes_per_sector' => (isset($disk['BytesPerSector']) && $disk['BytesPerSector'] !== null) ? $disk['BytesPerSector'] : 0,
                    'total_tracks' => (isset($disk['TotalTracks']) && $disk['TotalTracks'] !== null) ? $disk['TotalTracks'] : 0,
                    'total_cylinders' => (isset($disk['TotalCylinders']) && $disk['TotalCylinders'] !== null) ? $disk['TotalCylinders'] : 0,
                    'scsi_bus' => (isset($disk['SCSIBus']) && $disk['SCSIBus'] !== null) ? $disk['SCSIBus'] : 0,
                    'signature' => (isset($disk['Signature']) && $disk['Signature'] !== null) ? $disk['Signature'] : 0,
                    'total_sectors' => (isset($disk['TotalSectors']) && $disk['TotalSectors'] !== null) ? $disk['TotalSectors'] : 0,
                    'sectors_per_track' => (isset($disk['SectorsPerTrack']) && $disk['SectorsPerTrack'] !== null) ? $disk['SectorsPerTrack'] : 0,
                    'manufacturer' => (isset($disk['Manufacturer']) && $disk['Manufacturer'] !== null) ? $disk['Manufacturer'] : 0,
                    'capabilities' => (isset($disk['CapabilityDescriptions']) && $disk['CapabilityDescriptions'] !== null) ? implode(',', $disk['CapabilityDescriptions']) : 0,
                )
            );
        }
    }
}