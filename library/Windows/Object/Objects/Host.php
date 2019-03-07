<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Db;

class Host
{

    protected $hostname;

    protected $host_id    = -1;

    protected $approved   = false;

    protected $host_exist = false;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
        $this->init();
    }

    private function init()
    {
        $db = Db::newConfiguredInstance();

        $query = $db->select()
            ->from(
                'host_list',
                array(
                    'host_id',
                    'approved'
                )
            )->where(
                'host',
                $this->hostname
            );

        $host = $query->fetchRow();

        if ($host === false) {
            return $this;
        }

        $this->host_id = $host->host_id;
        $this->host_exist = true;

        if ($host->approved == 1) {
            $this->approved = true;
        }

        return $this;
    }

    public function name()
    {
        return $this->hostname;
    }

    public function exist()
    {
        return $this->host_exist;
    }

    public function approved()
    {
        return $this->approved;
    }
}