<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\PerfCounter\PerfCounter;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Network extends BaseClass
{
    protected $host_id;

    protected $hostname;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * @var WindowsDB
     */
    protected $db;

    protected $physical_interfaces = array();

    public function __construct($hostname)
    {
        $this->dbHelper = DbHelper::getInstance();
        $this->db = WindowsDB::fromConfig();
        $this->hostname = $hostname;
        $this->host_id = $this->dbHelper->getHostIdByName($hostname);
    }

    public function loadPhysicalFromDb()
    {
        $query = $this->db->select()
            ->from(
                'host_interfaces',
                array(
                    'name',
                    'interface_index',
                    'mac_address',
                    'physical_adapter',
                    'device_id',
                    'net_connection_id',
                    'net_connection_status',
                    'pnp_device_id',
                    'speed',
                    'service_name',
                    'network_addresses',
                    'adapter_type',
                    'manufacturer',
                    'manufacturer'
                )
            )->where(
                'host_id',
                $this->host_id
            )->where(
                'physical_adapter',
                1
            )->order(
                'net_connection_status',
                'ASC'
            );

        $rows = $query->fetchAll();

        $perfCounter = new PerfCounter($this->hostname);

        foreach ($rows as $row) {
            $interface = new NetworkInterface($row);

            $perfCounter->loadReferenceCounterFromDB(
                $row->name,
                array('value')
            );

            $interface->setPerformanceCounterReference($perfCounter);

            $this->physical_interfaces += array(
                $row->name => $interface
            );
        }
    }

    public function getPhysicalInterfaces()
    {
        return $this->physical_interfaces;
    }

    public function getPhysicalInterface($name)
    {
        if (isset($this->physical_interfaces[$name])) {
            return $this->physical_interfaces[$name];
        }
        return (new NetworkInterface(null));
    }

    public function parseApiRequest($content)
    {
        if (isset($content['output']) == false) {
            return;
        }

        $counter = new PerfCounter($this->hostname);
        $counter->loadPerformanceCounterHelpIndex();

        if (isset($content['output']['FullList']['interfaces'])) {
            if (Count($content['output']['FullList']['interfaces']) != 0) {

                $this->db->delete(
                    'host_interfaces',
                    Filter::expression('host_id', '=', $this->host_id)
                );

                foreach ($content['output']['FullList']['interfaces'] as $name => $interface) {
                    $counter->flushCounterReferencesFromDb($name);
                    foreach ($interface as $key => $_counter) {
                        if (is_array($_counter)) {
                            $counter->parsePerfCounter($interface, $name);
                        }
                    }

                    $this->addInterfaceToDb($name, $interface);
                }

                return;
            }
        }

        // TODO: This should only contain modified components
        if (isset($content['output']['Modified']['interfaces'])) {
            if (Count($content['output']['Modified']['interfaces']) != 0) {

                $this->db->delete(
                    'host_interfaces',
                    Filter::expression('host_id', '=', $this->host_id)
                );

                foreach ($content['output']['Modified']['interfaces'] as $name => $interface) {
                    $counter->flushCounterReferencesFromDb($name);
                    foreach ($interface as $key => $_counter) {
                        if (is_array($_counter)) {
                            $counter->parseCounterObject($key, $_counter, $name);
                        }
                    }

                    $this->addInterfaceToDb($name, $interface);
                }
            }
        }
    }

    protected function addInterfaceToDb($name, $nic)
    {
        $queryColumns = array(
            'host_id' => $this->host_id,
            'name' => $name,
        );

        $physical = 0;

        if (isset($nic['PhysicalAdapter'])) {
            if ($nic['PhysicalAdapter'] == true) {
                $physical = 1;
            }
        }
        $queryColumns = array_merge(
            $queryColumns,
            array(
                'physical_adapter' => $physical
            )
        );

        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'interface_index', $nic, 'InterfaceIndex', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'mac_address', $nic, 'MACAddress', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'device_id', $nic, 'DeviceID', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'net_connection_id', $nic, 'NetConnectionID', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'net_connection_status', $nic, 'NetConnectionStatus', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'pnp_device_id', $nic, 'PNPDeviceID', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'speed', $nic, 'Speed', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_name', $nic, 'ServiceName', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'network_addresses', $nic, 'NetworkAddresses', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'adapter_type', $nic, 'AdapterType', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'manufacturer', $nic, 'Manufacturer', '');

        $this->db->insert(
            'host_interfaces',
            $queryColumns
        );
    }
}