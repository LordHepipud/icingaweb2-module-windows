<?php

namespace Icinga\Module\Windows\Helper;

use Icinga\Module\Windows\WindowsDB;
use Exception;

class DbHelper
{
    /**
     * @var WindowsDB
     */
    protected $db;

    private static $instance = null;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->db = WindowsDB::fromConfig();
    }

    public function getHostIdByName($hostname)
    {
        $query = $this->db->select()
            ->from(
                'host_list',
                array('host_id')
            )->where(
                'host',
                $hostname
            );

        $row = $query->fetchRow();
        return $row->host_id;
    }

    public function addQueryColumn($dbQuery, $column, $object, $key, $default = null)
    {
        if (isset($object[$key]) === false) {
            if ($default == null) {
                return $dbQuery;
            }

            return array_merge(
                $dbQuery,
                array(
                    $column => $default
                )
            );
        }

        return array_merge(
            $dbQuery,
            array(
                $column => $object[$key]
            )
        );
    }

    public function addQueryBoolean($dbQuery, $column, $object, $key)
    {
        if (isset($object[$key]) == false) {
            return array_merge(
                $dbQuery,
                array(
                    $column => 0
                )
            );
        }

        if ($object[$key] == 1 || $object[$key] == 'yes' || $object[$key] == 'true' || $object[$key] == true) {
            return array_merge(
                $dbQuery,
                array(
                    $column => 1
                )
            );
        }

        return array_merge(
            $dbQuery,
            array(
                $column => 1
            )
        );
    }

    public function addQueryArray($dbQuery, $column, $object, $key)
    {
        if (isset($object[$key]) == false) {
            return array_merge(
                $dbQuery,
                array(
                    $column => ''
                )
            );
        }

        if (is_array($object[$key]) == false) {
            return array_merge(
                $dbQuery,
                array(
                    $column => ''
                )
            );
        }

        return array_merge(
            $dbQuery,
            array(
                $column => implode(',', $object[$key])
            )
        );
    }

    public function getQueryArray($entry)
    {
        if (is_array($entry) == false) {
            return array();
        }

        return explode(',', $entry);
    }

    public function isDbConfigured()
    {
        if ($this->db == null) {
            return false;
        }

        try {
            $this->db->select()->from('host_list', array('host_id'))->fetchOne();
        } catch (Exception $_) {
            return false;
        }

        return true;
    }

    public function hasReferences($reference, $hostname)
    {
        $host_id = $this->getHostIdByName($hostname);
        $query = $this->db->select()
            ->from(
                'host_perf_counter',
                array(
                    'host_id'
                )
            )->where(
                'host_id',
                $host_id
            )->where(
                'reference',
                $reference
            );

        return $query->fetchRow();
    }
}