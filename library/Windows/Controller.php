<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows;

use dipl\Html\Error;
use dipl\Web\CompatController;
use Exception;
use Icinga\Application\Icinga;
use Icinga\Exception\IcingaException;
use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Web\View;
use Icinga\Data\Filter\Filter;

class Controller extends CompatController
{
    /** @var Db */
    private $db;

    /** @var View */
    public $view;

    /** @var  MonitoringBackend */
    protected $monitoringBackend;

    /**
     * @return Db
     * @throws \Icinga\Exception\ConfigurationError
     */
    protected function getDb()
    {
        return Db::newConfiguredInstance();
    }

    protected function db()
    {
        if ($this->db === null) {
            try {
                $this->db = Db::newConfiguredInstance();
                $migrations = new Db\Migrations($this->db);
                if (! $migrations->hasSchema()) {
                    $this->redirectToConfiguration();
                }
            } catch (Exception $e) {
                $this->redirectToConfiguration();
            }
        }

        return $this->db;
    }

    protected function redirectToConfiguration()
    {
        if ($this->getRequest()->getControllerName() !== 'config') {
            $this->redirectNow('windows/config');
        }
    }

    protected function runFailSafe($callback)
    {
        try {
            if (is_string($callback) && method_exists($this, $callback)) {
                $this->$callback();
            } else {
                $callback();
            }
        } catch (\Error $e) {
            $this->content()->add(Error::show($e));
        } catch (\Exception $e) {
            $this->content()->add(Error::show($e));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRestrictions($name, $permission = null)
    {
        $restrictions = array();
        if ($this->Auth()->isAuthenticated()) {
            foreach ($this->Auth()->getUser()->getRoles() as $role) {
                if ($permission !== null && ! in_array($permission, $role->getPermissions())) {
                    continue;
                }
                $restrictionsFromRole = $role->getRestrictions($name);
                if (empty($restrictionsFromRole)) {
                    $restrictions = array();
                    break;
                } else {
                    if (! is_array($restrictionsFromRole)) {
                        $restrictionsFromRole = array($restrictionsFromRole);
                    }
                    $restrictions = array_merge($restrictions, array_values($restrictionsFromRole));
                }
            }
        }
        return $restrictions;
    }

    /**
     * Retrieves the Icinga MonitoringBackend
     *
     * @param string|null $name
     *
     * @return MonitoringBackend
     * @throws IcingaException When monitoring is not enabled
     */
    protected function monitoringBackend($name = null)
    {
        if ($this->monitoringBackend === null) {
            if (! Icinga::app()->getModuleManager()->hasEnabled('monitoring')) {
                throw new IcingaException('The module "monitoring" must be enabled and configured!');
            }
            $this->monitoringBackend = MonitoringBackend::instance($name);
        }
        return $this->monitoringBackend;
    }


    protected function setHostApproved($host, $approved)
    {
        $updateFilter = Filter::expression('host', '=', $host);
        $this->getDb()->update(
            'host_list',
            array(
                'approved' => $approved
            ),
            $updateFilter
        );
    }

    protected function setAllHostsApprovalStatus($status)
    {
        $this->getDb()->update(
            'host_list',
            array(
                'approved' => $status
            )
        );
    }

    protected function addMainTabs($name)
    {
        $this->tabs()->add('host', array(
            'active'    => $name === 'host',
            'title'     => $this->translate('Details', 'Tab title'),
            'url'       => \Icinga\Web\Url::fromRequest()->setPath('windows/host')
        ));

        $this->tabs()->add('processes', array(
            'active'    => $name === 'processes',
            'title'     => $this->translate('Processes', 'Tab title'),
            'url'       => \Icinga\Web\Url::fromRequest()->setPath('windows/processes')
        ));

        $this->tabs()->add('services', array(
            'active'    => $name === 'services',
            'title'     => $this->translate('Services', 'Tab title'),
            'url'       => \Icinga\Web\Url::fromRequest()->setPath('windows/services')
        ));

        $this->tabs()->add('updates', array(
            'active'    => $name === 'updates',
            'title'     => $this->translate('Updates', 'Tab title'),
            'url'       => \Icinga\Web\Url::fromRequest()->setPath('windows/updates')
        ));
    }
}
