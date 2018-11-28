<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows;

use Icinga\Application\Config;
use Icinga\Data\ConfigObject;
use Icinga\Data\Filter\Filter;
use Icinga\Data\Filter\FilterExpression;
use Icinga\Data\ResourceFactory;
use Icinga\Exception\ConfigurationError;
use Icinga\Repository\DbRepository;
use Icinga\Repository\RepositoryQuery;

class WindowsDB extends DbRepository
{
    /**
     * {@inheritdoc}
     */
    const DATETIME_FORMAT = 'Y-m-d G:i:s';

    /**
     * {@inheritdoc}
     */
    protected $tableAliases = array(
        'version'              => 'v',
        'host_list'            => 'hl',
        'host_check_results'   => 'hcr',
        'global_module_checks' => 'gmc',
        'host_module_checks'   => 'hmc',
        'host_bios'            => 'b',
    );

    /**
     * Default query columns
     *
     * @var array
     */
    protected static $defaultQueryColumns = array(
        'version'   => array(
            'schema'
        ),
        'host_list' => array(
            'host_id',
            'host',
            'port',
            'address',
            'approved',
            'os',
            'version',
            'user'
        ),
        'host_token_list' => array(
            'host_id',
            'token',
            'created'
        ),
        'global_module_checks' => array(
            'id',
            'name',
            'check_interval',
            'enabled'
        ),
        'host_module_checks' => array(
            'host_id',
            'name',
            'check_interval',
            'enabled'
        ),
        'host_check_results' => array(
            'host_id',
            'module',
            'result',
            'timestamp'
        ),
        'available_modules' => array(
            'id',
            'name'
        ),
        'host_process_list' => array(
            'host_id',
            'proc_name',
            'proc_id',
            'proc_priority',
            'proc_threads',
            'proc_processor_percent',
            'proc_processor_time',
            'proc_pagefile',
            'proc_used_memory',
            'proc_required_memory',
            'proc_cmd'
        ),
        'host_pending_updates' => array(
            'host_id',
            'name',
            'description',
            'kbarticles',
            'uninst_note',
            'support_url',
            'require_reboot',
            'download_size',
            'downloaded',
            'superseded_ids'
        ),
        'host_update_history' => array(
            'host_id',
            'name',
            'description',
            'result',
            'support_url',
            'installed_on',
            'internal_type'
        ),
        'host_hotfix_history' => array(
            'host_id',
            'id',
            'name',
            'description',
            'status',
            'install_date',
            'support_url',
            'fix_comment',
            'service_pack',
            'installed_by'
        ),
        'host_hardware_cpu' => array(
            'host_id',
            'cpu_id',
            'architecture',
            'l2_cache_size',
            'description',
            'processor_id',
            'current_clock_speed',
            'max_clock_speed',
            'virtualization_enabled',
            'number_cores',
            'number_logical_processors',
            'thread_count',
            'manufacturer',
            'caption',
            'revision',
            'address_width',
            'family',
            'level',
            'cpu_status',
            'name'
        ),
        'host_hardware_memory' => array(
            'host_id',
            'bank_label',
            'capacity',
            'memory_tye',
            'total_width',
            'clock_speed',
            'description',
            'tag',
            'location',
            'caption',
            'serial',
            'part_number',
            'min_voltage',
            'max_voltage',
            'configured_voltage',
            'manufacturer'
        ),
        'host_hardware_disk' => array(
            'host_id',
            'disk_id',
            'description',
            'firmware_revision',
            'drive_reference',
            'caption',
            'total_heads',
            'model',
            'size',
            'partitions',
            'serial_number',
            'scsi_target_id',
            'scsi_logical_id',
            'scsi_port',
            'media_type',
            'bytes_per_sector',
            'total_tracks',
            'total_cylinders',
            'scsi_bus',
            'signature',
            'total_sectors',
            'sectors_per_track',
            'manufacturer',
            'capabilities'
        ),
        'host_bios' => array(
            'host_id',
            'embedded_controller_major_version',
            'embedded_controller_minor_version',
            'description',
            'software_element_state',
            'smbios_major_version',
            'smbios_minor_version',
            'bios_characteristics',
            'system_bios_major_version',
            'system_bios_minor_version',
            'version',
            'smbios_version',
            'primary_bios',
            'smbios_present',
            'current_language',
            'available_languages',
            'installable_languages',
            'status',
            'caption',
            'release_date',
            'manufacturer',
            'software_element_id',
            'name',
            'serial_number',
            'bios_version'
        ),
        'host_system' => array(
            'host_id',
            'system_device',
            'os_language',
            'architecture',
            'number_of_users',
            'root_dir',
            'system_dir',
            'os_type',
            'number_of_licensed_users',
            'system_name',
            'build_type',
            'service_pack_major_version',
            'service_pack_minor_version',
            'version',
            'country_code',
            'build_number',
            'install_date',
            'registered_user',
            'serial_number',
            'system_drive',
            'os_sku',
            'local_datetime',
            'caption',
            'is_primary',
            'encryption_level',
            'data_execution_prevention_available',
            'description',
            'boot_device',
            'manufacturer',
            'code_set',
            'name',
            'languages',
            'last_boot_time',
            'locale',
        ),
        'host_perf_counter' => array(
            'host_id',
            'category',
            'instance',
            'counter',
            'raw_value',
            'base_value',
            'system_frequency',
            'counter_frequency',
            'counter_timestamp',
            'timestamp',
            'timestamp_100nsec',
            'value',
            'reference'
        ),
        'host_perf_counter_help' => array(
            'counter',
            'counter_type',
            'type',
            'help'
        ),
        'host_interfaces' => array(
            'host_id',
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
            'manufacturer'
        ),
        'host_service_list' => array(
            'host_id',
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
            'start_type',
        )
    );

    /**
     * {@inheritdoc}
     */
    protected function initializeQueryColumns()
    {
        $additionalColumns = Config::module('windows', 'columns')->keys();
        $queryColumns = static::$defaultQueryColumns;

        /*if ($additionalColumns !== null) {
            $eventColumns = $queryColumns['event'];
            $queryColumns['event'] = array_merge($eventColumns, array_diff($additionalColumns, $eventColumns));
        }*/
        return $queryColumns;
    }

    /**
     * Create and return a new instance of the WindowsDB
     *
     * @param   ConfigObject    $config     The configuration to use, otherwise the module's configuration
     *
     * @return  static
     *
     * @throws  ConfigurationError          In case no resource has been configured in the module's configuration
     */
    public static function fromConfig(ConfigObject $config = null)
    {
        if ($config === null) {
            $moduleConfig = Config::module('windows');
            if (($resourceName = $moduleConfig->get('db', 'resource')) === null) {
                return null;
            }

            $resource = ResourceFactory::create($resourceName);
        } else {
            $resource = ResourceFactory::createResource($config);
        }

        return new static($resource);
    }

    /**
     * Convert an IP address into its human-readable form
     *
     * @param   string  $rawAddress
     *
     * @return  string
     */
    protected function retrieveIpAddress($rawAddress)
    {
        return $rawAddress === null ? null : inet_ntop($rawAddress);
    }

    /**
     * Convert an IP address into its binary form
     *
     * @param   string  $address
     *
     * @return  string
     */
    protected function persistIpAddress($address)
    {
        return $address === null ? null : inet_pton($address);
    }
}
