<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\PerfCounter\PerfCounterObject;

class NetworkInterface
{
    protected $name;

    protected $interface_index;

    protected $mac_address;

    protected $physical_adapter;

    protected $device_id;

    protected $net_connection_id;

    protected $net_connection_status;

    protected $pnp_device_id;

    protected $speed;

    protected $service_name;

    protected $network_addresses;

    protected $adapter_type;

    protected $manufacturer;

    protected $references;

    public function __construct($db_result)
    {
        if ($db_result == null) {
            return;
        }
        $this->setProperty($db_result, 'name');
        $this->setProperty($db_result, 'interface_index');
        $this->setProperty($db_result, 'mac_address');
        $this->setProperty($db_result, 'physical_adapter');
        $this->setProperty($db_result, 'device_id');
        $this->setProperty($db_result, 'net_connection_id');
        $this->setProperty($db_result, 'net_connection_status');
        $this->setProperty($db_result, 'pnp_device_id');
        $this->setProperty($db_result, 'speed');
        $this->setProperty($db_result, 'service_name');
        $this->setProperty($db_result, 'network_addresses');
        $this->setProperty($db_result, 'adapter_type');
        $this->setProperty($db_result, 'manufacturer');
    }

    protected function setProperty($db_result, $property)
    {
        if (property_exists($db_result, $property)) {
            $this->$property = $db_result->$property;
        }
    }

    public function setPerformanceCounterReference($reference)
    {
        $this->references = $reference;
    }

    public function getReference($reference)
    {
        if ($this->references == null) {
            return (new PerfCounterObject(null));
        }

        return $this->references->getReference($reference);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getInterfaceIndex()
    {
        return $this->interface_index;
    }

    /**
     * @param mixed $interface_index
     */
    public function setInterfaceIndex($interface_index)
    {
        $this->interface_index = $interface_index;
    }

    /**
     * @return mixed
     */
    public function getMacAddress()
    {
        return $this->mac_address;
    }

    /**
     * @param mixed $mac_address
     */
    public function setMacAddress($mac_address)
    {
        $this->mac_address = $mac_address;
    }

    /**
     * @return mixed
     */
    public function getPhysicalAdapter()
    {
        return $this->physical_adapter;
    }

    /**
     * @param mixed $physical_adapter
     */
    public function setPhysicalAdapter($physical_adapter)
    {
        $this->physical_adapter = $physical_adapter;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->device_id;
    }

    /**
     * @param mixed $device_id
     */
    public function setDeviceId($device_id)
    {
        $this->device_id = $device_id;
    }

    /**
     * @return mixed
     */
    public function getNetConnectionId()
    {
        return $this->net_connection_id;
    }

    /**
     * @param mixed $net_connection_id
     */
    public function setNetConnectionId($net_connection_id)
    {
        $this->net_connection_id = $net_connection_id;
    }

    /**
     * @return mixed
     */
    public function getNetConnectionStatus()
    {
        return $this->net_connection_status;
    }

    /**
     * @param mixed $net_connection_status
     */
    public function setNetConnectionStatus($net_connection_status)
    {
        $this->net_connection_status = $net_connection_status;
    }

    /**
     * @return mixed
     */
    public function getPnpDeviceId()
    {
        return $this->pnp_device_id;
    }

    /**
     * @param mixed $pnp_device_id
     */
    public function setPnpDeviceId($pnp_device_id)
    {
        $this->pnp_device_id = $pnp_device_id;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @param mixed $speed
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    }

    /**
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * @param mixed $service_name
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;
    }

    /**
     * @return mixed
     */
    public function getNetworkAddresses()
    {
        return $this->network_addresses;
    }

    /**
     * @param mixed $network_addresses
     */
    public function setNetworkAddresses($network_addresses)
    {
        $this->network_addresses = $network_addresses;
    }

    /**
     * @return mixed
     */
    public function getAdapterType()
    {
        return $this->adapter_type;
    }

    /**
     * @param mixed $adapter_type
     */
    public function setAdapterType($adapter_type)
    {
        $this->adapter_type = $adapter_type;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    public function getNetConnectionStatusDescription()
    {
        switch ($this->net_connection_status) {
            case 0:
                return 'Adapter is disconnected';
            case 1:
                return 'Adapter is connecting';
            case 2:
                return 'Adapter is connected';
            case 3:
                return 'Adapter is disconnecting';
            case 4:
                return 'Adapter hardware is not present';
            case 5:
                return 'Adapter hardware is disabled';
            case 6:
                return 'Adapter has a hardware malfunction';
            case 7:
                return 'Media is disconnected';
            case 8:
                return 'Adapter is authenticating';
            case 9:
                return 'Authentication has succeeded';
            case 10:
                return 'Authentication has failed';
            case 11:
                return 'Address is invalid';
            case 12:
                return 'Credentials are required';
            case 13:
                return 'Other unspecified state';
        }

        return 'Unknown adapter state';
    }
}