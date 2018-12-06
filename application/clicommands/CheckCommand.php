<?php

namespace Icinga\Module\Windows\Clicommands;

use Icinga\Module\Windows\Object\Objects\Memory;
use Icinga\Module\Windows\Object\Objects\Processes;
use Icinga\Module\Windows\Object\Objects\Services;
use Icinga\Module\Windows\Object\Objects\Updates;
use Icinga\Module\Windows\Object\Objects\Cpu;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\PerfCounter\PerfCounter;


/**
 * Windows Check Command
 */
class CheckCommand extends CommandBase
{
    /**
     * Check Pending Updates Count
     *
     * USAGE
     *
     * icingacli windows check pendingupdates [--host <name>] [--warning <count>] [--critical <count>]
     */
    public function pendingupdatesAction()
    {
        $host = $this->params->getRequired('host');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');

        $exitcode = 0;

        $updates = new Updates($host);
        $updates->loadPendingUpdatesFromDB();

        $pendingupdates = Count($updates->getPendingUpdates());

        if ($pendingupdates >= $critical && $critical != null) {
            $exitcode = 2;
        } else if ($pendingupdates >= $warning && $warning != null) {
            $exitcode = 1;
        }

        Tools::getInstance()->getCliOutputMessage(
        'There are ' . $pendingupdates . ' updates pending for installation on this system.',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Installed Hotfix
     *
     * USAGE
     *
     * icingacli windows check hotfix [--host <name>] [--hotfix <id>] [--required <0|1>]
     */
    public function hotfixAction()
    {
        $host = $this->params->getRequired('host');
        $hotfixId = $this->params->getRequired('hotfix');
        $required = $this->params->get('required');

        if ($required == null) {
            $required = true;
        } else {
            $required = boolval($required);
        }

        $exitcode = 0;

        $hotfix = new Updates($host);

        $installed = $hotfix->loadHotfixesFromDB($hotfixId);

        if ($installed == null && $required == true) {
            $exitcode = 2;
        } else if ($installed != null && $required == false) {
            $exitcode = 2;
        }

        Tools::getInstance()->getCliOutputMessage(
            'Hotfix ' . $hotfixId . ' is' . ($installed == null ? ' not' : '') . ' installed on this system.',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Installed Hotfix
     *
     * USAGE
     *
     * icingacli windows check update [--host <name>] [--update <id>] [--required <0|1>]
     */
    public function updateAction()
    {
        $host = $this->params->getRequired('host');
        $updatename = $this->params->getRequired('update');
        $required = $this->params->get('required');

        if ($required == null) {
            $required = true;
        } else {
            $required = boolval($required);
        }

        $updatename = strtolower($updatename);
        $DBUpdateName = $updatename;

        $exitcode = 0;

        $updates = new Updates($host);

        $updatelist = $updates->loadUpdateHistoryFromDB();
        $installed = false;

        foreach($updatelist as $index => $update) {
            $name = strtolower($update->getName());

            if (strpos($name, $updatename) !== false) {
                $installed = true;
                $DBUpdateName = $update->getName();
                break;
            }
        }

        if ($installed == false && $required == true) {
            $exitcode = 2;
        } else if ($installed == true && $required == false) {
            $exitcode = 2;
        }

        Tools::getInstance()->getCliOutputMessage(
            'Update "' . $DBUpdateName . '" is' . ($installed == false ? ' not' : '') . ' installed on this system.',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Load of Host System
     *
     * USAGE
     *
     * icingacli windows check load [--host <name>] [--core <id>] [--warning <int>]  [--critical <int>]
     */
    public function loadAction()
    {
        $host = $this->params->getRequired('host');
        $core = $this->params->get('core');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');

        if ($core == null) {
            $core = '_Total';
        }

        $exitcode = 0;

        $cpu = new Cpu($host);
        $cpu->loadFromDb();

        $load = round($cpu->getCoreById($core)->getValue(), 2);

        if ($load >= $critical && $critical != null) {
            $exitcode = 2;
        } else if ($load >= $warning && $warning != null) {
            $exitcode = 1;
        }

        Tools::getInstance()->getCliOutputMessage(
            'Load is ' . $load . '%.',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Memory of Host System
     *
     * USAGE
     *
     * icingacli windows check memory [--host <name>] [--warning <int MB>] [--critical <int MB>] [--warning_percent <int>]  [--critical_percent <int>]
     */
    public function memoryAction()
    {
        $host = $this->params->getRequired('host');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $warning_percent = $this->params->get('warning_percent');
        $critical_percent = $this->params->get('critical_percent');

        $exitcode = 0;

        $memory = new Memory($host);
        $memory->loadFromDb();

        $freeMemory = round(Tools::getInstance()->convertBytesToMB($memory->getFreeMemory()), 2);
        $usedMemory =  round(Tools::getInstance()->convertBytesToMB($memory->getUsedMemory()), 2);
        $totalMemory = Tools::getInstance()->convertBytesToMB($memory->getTotalMemory());
        $percentFree = round($freeMemory * 100 / $totalMemory, 2);

        if ($warning_percent != null || $critical_percent != null) {
            if ($percentFree <= $critical_percent && $critical_percent != null) {
                $exitcode = 2;
            } else if ($percentFree <= $warning_percent && $warning_percent != null) {
                $exitcode = 1;
            }
        } else {
            if ($freeMemory <= $critical && $critical != null) {
                $exitcode = 2;
            } else if ($freeMemory <= $warning && $warning != null) {
                $exitcode = 1;
            }
        }

        Tools::getInstance()->getCliOutputMessage(
            'The current memory usage is ' . $usedMemory .
            ' MB out of ' . $totalMemory . ' MB (' .
            $freeMemory . ' MB free, ' . $percentFree . '%)',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Services of Host System
     *
     * USAGE
     *
     * icingacli windows check service [--host <name>] [--service <name>] [--status <int>]
     */
    public function serviceAction()
    {
        $host = $this->params->getRequired('host');
        $serviceName = $this->params->getRequired('service');
        $status = $this->params->get('status');

        // Use Running state as default if we did not specify it
        if ($status == null) {
            $status = 4;
        }

        $service = new Services($host);

        $result = $service->getService($serviceName, false);
        $exitcode = 0;

        if ($result == false) {
            Tools::getInstance()->getCliOutputMessage(
                'Service ' . $serviceName . ' is not yet loaded from this host.',
                $exitcode
            );
        } else {
            if ($result->status != $status) {
                $exitcode = 2;
            }
            Tools::getInstance()->getCliOutputMessage(
                'Service ' . $result->display_name . ' (' . $result->service_name . ') is currently ' . $service->getServiceStatus($result->status),
                $exitcode
            );
        }

        exit($exitcode);
    }

    /**
     * Check Processes of Host System
     *
     * USAGE
     *
     * icingacli windows check process [--host <name>] [--process <name>] [--negate <0|1>] [--warning <int>]  [--critical <int>]
     */
    public function processAction()
    {
        $host = $this->params->getRequired('host');
        $process = $this->params->getRequired('process');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $negate = $this->params->get('negate');

        if ($negate == null) {
            $negate = 0;
        }

        $exitcode = 0;
        $processes = new Processes($host);

        $result = $processes->loadSingleDB($process);

        $processCount = Count($result);
        $proc = current($result);

        if ($negate == 0) {
            if ($processCount <= $critical && $critical != null) {
                $exitcode = 2;
            } else if ($processCount <= $warning && $warning != null) {
                $exitcode = 1;
            }
        } elseif ($negate == 1) {
            if ($processCount >= $critical && $critical != null) {
                $exitcode = 2;
            } else if ($processCount >= $warning && $warning != null) {
                $exitcode = 1;
            }
        }

        Tools::getInstance()->getCliOutputMessage(
            'There are ' . $processCount . ' instance(s) of process ' . $proc->getName() . ' pending for installation on this system.',
            $exitcode
        );

        exit($exitcode);
    }

    /**
     * Check Processes of Host System
     *
     * USAGE
     *
     * icingacli windows check process [--host <name>] [--process <name>] [--negate <0|1>] [--warning <int>]  [--critical <int>]
     */
    public function counterAction()
    {
        $host = $this->params->getRequired('host');
        $counterName = $this->params->getRequired('counter');
        $category = $this->params->get('category');
        $instance = $this->params->get('instance');
        $reference = $this->params->get('reference');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $negate = $this->params->get('negate');

        if ($negate == null) {
            $negate = 0;
        }

        $exitcode = 0;

        $perfCounter = new PerfCounter($host);
        $path = '';
        $value = 0;

        if ($reference != null) {
            $path = $perfCounter->compileReferenceCounterPath($reference, $counterName);
            $perfCounter->loadReferenceCounterFromDB($reference, array('value'));
            $value = $perfCounter->getReference($path)->getValue();
        } else {
            $path = $perfCounter->compilePerformanceCounterPath($category, $instance, $counterName);
            $perfCounter->loadCounterFromDB(array($path), array('value'));

            $value = $perfCounter->getCounter($path)->getValue();
        }

        if ($perfCounter->hasReference($path) == false && $perfCounter->hasCounter($path) == false) {
            $exitcode = 1;
            Tools::getInstance()->getCliOutputMessage(
                'The counter "' . $path . '" does not exist.',
                $exitcode
            );

            exit($exitcode);
        }

        $value = round($value, 2);

        if ($negate == 0) {
            if ($value <= $critical && $critical != null) {
                $exitcode = 2;
            } else if ($value <= $warning && $warning != null) {
                $exitcode = 1;
            }
        } elseif ($negate == 1) {
            if ($value >= $critical && $critical != null) {
                $exitcode = 2;
            } else if ($value >= $warning && $warning != null) {
                $exitcode = 1;
            }
        }

        Tools::getInstance()->getCliOutputMessage(
            'The value of counter "' . $path . '" is ' . $value,
            $exitcode
        );

        exit($exitcode);
    }
}