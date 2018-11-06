<?php

namespace Icinga\Module\Windows\Object;

use Icinga\Module\Windows\WindowsDB;

class WindowsHost
{
    protected $hostname;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadCheckResultFromDb($module)
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_list',
                array('host_id')
            )->where(
                'host',
                $this->hostname
            );

        $host = $query->fetchRow();
        $this->host_id = $host->host_id;

        $query = $db->select()
            ->from(
                'host_check_results',
                array('result')
            )->where(
                'host_id',
                $this->host_id
            )->where(
                'module',
                $module
            );

        $result = $query->fetchRow();

        if ($result == false) {
            return null;
        }

        return json_decode($result->result, true);
    }
}