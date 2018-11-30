<?php

namespace Icinga\Module\Windows\Data;

use Icinga\Module\Windows\Core\RestApiClient;

class HostApi
{
    protected $host;

    protected $port;

    protected $client;

    public function __construct($host, $port = 5891)
    {
        $this->host = $host;
        $this->port = $port;

        $this->client = new RestApiClient(
            $this->host,
            $this->port
        );
    }

    public function getCPUInformation()
    {
        return $this->client->request('GET', 'data?include=cpu');
    }

    public function getHardwareInformation()
    {
        return $this->client->request('GET', 'data?include=hardware');
    }

    public function getMemoryInformation()
    {
        return $this->client->request('GET', 'data?include=memory');
    }

    public function getProcessInformation()
    {
        return $this->client->request('GET', 'data?include=process');
    }

    public function getCustomData($modules)
    {
        if ($modules === null || isset($modules) == false) {
            return null;
        }

        $moduleList = '';
        foreach ($modules as $module) {
            $moduleList = $moduleList . $module . ',';
        }
        $moduleList = substr($moduleList, 0,-1);
        return $this->client->request('GET', 'data?include=' . $moduleList);
    }

}