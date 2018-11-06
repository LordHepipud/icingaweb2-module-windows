<?php

namespace Icinga\Module\Windows\Clicommands;

use Icinga\Cli\Command;

class CommandBase extends Command
{
    public function init()
    {
        $this->app->getModuleManager()->loadEnabledModules();
    }
}
