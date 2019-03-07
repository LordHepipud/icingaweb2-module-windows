<?php

namespace Icinga\Module\Windows\Clicommands;

use Icinga\Module\Monitoring\Object\Service;
use Icinga\Module\Windows\Object\Objects\Host;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        $exitcode = Service::STATE_UNKNOWN;

        $updates = new Updates($host->name());
        $updates->loadPendingUpdatesFromDB();

        $pendingupdates = Count($updates->getPendingUpdates());

        if ($pendingupdates >= $critical && $critical != null) {
            $exitcode = Service::STATE_CRITICAL;
        } else if ($pendingupdates >= $warning && $warning != null) {
            $exitcode = Service::STATE_WARNING;
        } else {
            $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $hotfixId = $this->params->getRequired('hotfix');
        $required = $this->params->get('required');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        if ($required == null) {
            $required = true;
        } else {
            $required = boolval($required);
        }

        $exitcode = Service::STATE_UNKNOWN;

        $hotfix = new Updates($host->name());

        $installed = $hotfix->loadHotfixesFromDB($hotfixId);

        if ($installed == null && $required == true) {
            $exitcode = Service::STATE_CRITICAL;
        } else if ($installed != null && $required == false) {
            $exitcode = Service::STATE_CRITICAL;
        } else {
            $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $updatename = $this->params->getRequired('update');
        $required = $this->params->get('required');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        if ($required == null) {
            $required = true;
        } else {
            $required = boolval($required);
        }

        $updatename = strtolower($updatename);
        $DBUpdateName = $updatename;

        $exitcode = Service::STATE_UNKNOWN;

        $updates = new Updates($host->name());

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
            $exitcode = Service::STATE_CRITICAL;
        } else if ($installed == true && $required == false) {
            $exitcode = Service::STATE_CRITICAL;
        } else {
            $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $core = $this->params->get('core');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        if ($core == null) {
            $core = '_Total';
        }

        $exitcode = Service::STATE_UNKNOWN;

        $cpu = new Cpu($host->name());
        $cpu->loadFromDb();

        $load = round($cpu->getCoreById($core)->getValue(), 2);

        if ($load >= $critical && $critical != null) {
            $exitcode = Service::STATE_CRITICAL;
        } else if ($load >= $warning && $warning != null) {
            $exitcode = Service::STATE_WARNING;
        } else {
            $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $warning_percent = $this->params->get('warning_percent');
        $critical_percent = $this->params->get('critical_percent');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        $exitcode = Service::STATE_UNKNOWN;

        $memory = new Memory($host->name());
        $memory->loadFromDb();

        $freeMemory = round(Tools::getInstance()->convertBytesToMB($memory->getFreeMemory()), 2);
        $usedMemory =  round(Tools::getInstance()->convertBytesToMB($memory->getUsedMemory()), 2);
        $totalMemory = Tools::getInstance()->convertBytesToMB($memory->getTotalMemory());
        $percentFree = round($freeMemory * 100 / $totalMemory, 2);

        if ($warning_percent != null || $critical_percent != null) {
            if ($percentFree <= $critical_percent && $critical_percent != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($percentFree <= $warning_percent && $warning_percent != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
            }
        } else {
            if ($freeMemory <= $critical && $critical != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($freeMemory <= $warning && $warning != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $serviceName = $this->params->getRequired('service');
        $status = $this->params->get('status');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        // Use Running state as default if we did not specify it
        if ($status == null) {
            $status = 4;
        }

        $service = new Services($host->name());

        $result = $service->getService($serviceName, false);
        $exitcode = Service::STATE_OK;

        if ($result == false) {
            Tools::getInstance()->getCliOutputMessage(
                'Service ' . $serviceName . ' is not yet loaded from this host.',
                $exitcode
            );
        } else {
            if ($result->status != $status) {
                $exitcode = Service::STATE_CRITICAL;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $process = $this->params->getRequired('process');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $negate = $this->params->get('negate');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        if ($negate == null) {
            $negate = 0;
        }

        $exitcode = Service::STATE_UNKNOWN;
        $processes = new Processes($host->name());

        $result = $processes->loadSingleDB($process);

        $processCount = Count($result);
        $proc = current($result);

        if ($negate == 0) {
            if ($processCount <= $critical && $critical != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($processCount <= $warning && $warning != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
            }
        } elseif ($negate == 1) {
            if ($processCount >= $critical && $critical != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($processCount >= $warning && $warning != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
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
        $host = new Host(
            $this->params->getRequired('host')
        );
        $counterName = $this->params->getRequired('counter');
        $category = $this->params->get('category');
        $instance = $this->params->get('instance');
        $reference = $this->params->get('reference');
        $warning = $this->params->get('warning');
        $critical = $this->params->get('critical');
        $negate = $this->params->get('negate');

        if(!$host->exist() || !$host->approved()) {
            exit($this->exitUnknown($host));
        }

        if ($negate == null) {
            $negate = 0;
        }

        $exitcode = Service::STATE_UNKNOWN;

        $perfCounter = new PerfCounter($host->name());
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
            $exitcode = Service::STATE_WARNING;
            Tools::getInstance()->getCliOutputMessage(
                'The counter "' . $path . '" does not exist.',
                $exitcode
            );

            exit($exitcode);
        }

        $value = round($value, 2);

        if ($negate == 0) {
            if ($value <= $critical && $critical != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($value <= $warning && $warning != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
            }
        } elseif ($negate == 1) {
            if ($value >= $critical && $critical != null) {
                $exitcode = Service::STATE_CRITICAL;
            } else if ($value >= $warning && $warning != null) {
                $exitcode = Service::STATE_WARNING;
            } else {
                $exitcode = Service::STATE_OK;
            }
        }

        Tools::getInstance()->getCliOutputMessage(
            'The value of counter "' . $path . '" is ' . $value,
            $exitcode
        );

        exit($exitcode);
    }
}