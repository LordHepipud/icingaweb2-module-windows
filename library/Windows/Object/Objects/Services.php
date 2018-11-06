<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Services extends BaseClass
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

    protected $serviceList = array();

    public function __construct($hostname)
    {
        $this->dbHelper = DbHelper::getInstance();
        $this->db = WindowsDB::fromConfig();
        $this->hostname = $hostname;
        $this->host_id = $this->dbHelper->getHostIdByName($hostname);
    }

    public function getService($service, $all = false)
    {
        $queryColumns = array();
        if ($all) {
            $queryColumns = array(
                'display_name',
                'service_name',
                'dependent_services',
                'can_pause_and_continue',
                'service_handle',
                'depends_on',
                'can_stop',
                'service_type',
                'can_shutdown',
                'status',
                'start_type'
            );
        } else {
            $queryColumns = array(
                'display_name',
                'service_name',
                'status',
            );
        }
        $query = $this->db->select()
            ->from(
                'host_service_list',
                $queryColumns
            )->where(
                'host_id',
                $this->host_id
            )->where(
                'service_name',
                $service
            );

        return $query->fetchRow();
    }

    public function getServiceList()
    {
        return $this->serviceList;
    }

    public function getServiceCount()
    {
        return (Count($this->serviceList));
    }

    public function getServiceStatus($status)
    {
        switch($status)
        {
            case 1:
                return 'Stopped';
            case 2:
                return 'Starting';
            case 3:
                return 'Stopping';
            case 4:
                return 'Running';
            case 5:
                return 'Continue Pending';
            case 6:
                return 'Pause Pending';
            case 7:
                return 'Paused';
        }

        return $status;
    }

    public function parseApiRequest($content)
    {
        if (isset($content['output']) == false) {
            return;
        }

        if (isset($content['output']['FullList'])) {
            if (Count($content['output']['FullList']) != 0) {

                $this->db->delete(
                    'host_service_list',
                    Filter::expression('host_id', '=', $this->host_id)
                );

                foreach ($content['output']['FullList'] as $key => $service) {
                    $this->addServiceToDb($service);
                }

                return;
            }
        }

        if (isset($content['output']['Removed'])) {
            if (empty($content['output']['Removed']) == false) {

                foreach ($content['output']['Removed'] as $service) {

                    $this->db->delete(
                        'host_service_list',
                        Filter::matchAll(
                            Filter::expression('host_id', '=', $this->host_id),
                            Filter::where('service_name', $service)
                        )
                    );
                }
            }
        }

        if (isset($content['output']['Added'])) {
            if (Count($content['output']['Added']) != 0) {
                foreach ($content['output']['Added'] as $key => $service) {
                    $this->addServiceToDb($service);
                }
            }
        }

        if (isset($content['output']['Modified'])) {
            if (Count($content['output']['Modified']) != 0) {
                foreach ($content['output']['Modified'] as $key => $service) {
                    $this->updateService($service);
                }
            }
        }
    }

    protected function addServiceToDb($service)
    {
        if (is_array($service) === false) {
            return;
        }

        $queryColumns = array(
            'host_id' => $this->host_id
        );
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'display_name', $service, 'display_name', '');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_name', $service,'service_name', '');
        $queryColumns = $this->dbHelper->addQueryArray($queryColumns, 'dependent_services', $service, 'dependent_services');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_pause_and_continue', $service, 'can_pause_and_continue');
        //$queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_handle', $service, 'service_handle', '');
        $queryColumns = $this->dbHelper->addQueryArray($queryColumns, 'depends_on', $service, 'depends_on');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_stop', $service, 'can_stop');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_shutdown', $service, 'can_shutdown');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_type', $service, 'service_type', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'status', $service, 'status', 0);
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'start_type', $service, 'start_type', 0);

        $this->db->insert(
            'host_service_list',
            $queryColumns
        );
    }

    protected function updateService($service)
    {
        if (isset($service['service_name']) === false) {
            return;
        }

        $queryColumns = array();
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'display_name', $service, 'display_name');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_name', $service,'service_name');
        $queryColumns = $this->dbHelper->addQueryArray($queryColumns, 'dependent_services', $service, 'dependent_services');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_pause_and_continue', $service, 'can_pause_and_continue');
        //$queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_handle', $service, 'service_handle', '');
        $queryColumns = $this->dbHelper->addQueryArray($queryColumns, 'depends_on', $service, 'depends_on');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_stop', $service, 'can_stop');
        $queryColumns = $this->dbHelper->addQueryBoolean($queryColumns, 'can_shutdown', $service, 'can_shutdown');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'service_type', $service, 'service_type');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'status', $service, 'status');
        $queryColumns = $this->dbHelper->addQueryColumn($queryColumns, 'start_type', $service, 'start_type');

        $this->db->update(
            'host_service_list',
            $queryColumns,
            Filter::matchAll(
                Filter::expression('host_id', '=', $this->host_id),
                Filter::where('service_name', $service['service_name'])
            )
        );
    }
}