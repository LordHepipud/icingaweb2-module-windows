<?php

namespace Icinga\Module\Windows\Clicommands;

use Icinga\Cli\Command;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\Object\Objects\Host;

class CommandBase extends Command
{
    public function init()
    {
        $this->app->getModuleManager()->loadEnabledModules();
    }

    public function exitUnknown(Host $host)
    {
        if (!$host->exist()) {
            Tools::getInstance()->getCliOutputMessage(
                'Host ' . $host->name() . ' does not exist',
                Service::STATE_UNKNOWN
            );
            return Service::STATE_UNKNOWN;
        }

        if (!$host->approved()) {
            Tools::getInstance()->getCliOutputMessage(
                'Host ' . $host->name() . ' is not approved',
                Service::STATE_UNKNOWN
            );
            return Service::STATE_UNKNOWN;
        }
    }
}
