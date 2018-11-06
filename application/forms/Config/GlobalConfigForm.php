<?php
/* Icinga Web 2 - EventDB | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Forms\Config;

use Icinga\Forms\ConfigForm;
use Icinga\Web\Notification;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class GlobalConfigForm extends ConfigForm
{
    /**
     * @var WindowsDB
     */
    protected $db;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->db = WindowsDB::fromConfig();
        $this->dbHelper = DbHelper::getInstance();
        if ($this->dbHelper->isDbConfigured() == true) {
            $this->setSubmitLabel($this->translate('Save'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {
        if ($this->dbHelper->isDbConfigured() == false) {
            $this->addHint('You have to select a database from above first.');
            return;
        }

        $check_config = $this->getAvailableModules();

        foreach ($check_config as $config) {
            $value = $this->getGlobalCheckConfiguration($config->name);
            $enabled = 0;
            if ($value == false) {
                $value = 600;
                $enabled = 1;
            } else {
                $enabled = $value->enabled;
                $value = $value->check_interval;
            }

            $name = ucfirst($config->name);

            $this->addElement(
                'number',
                'value_' . $name,
                array(
                    'description' => $this->translate('Check intervall for the ' . $name . ' module'),
                    'label'       => $this->translate($name . ' Module'),
                    'value'       => $value
                )
            );
            $this->addElement(
                'checkbox',
                'enabled_' . $name,
                array(
                    'description' => $this->translate('Enable or disable module ' . $name),
                    'label'       => $this->translate('Enable ' . $name),
                    'value'       => $enabled
                )
            );
        }
    }

    public function onSuccess()
    {
        if ($this->save()) {
            Notification::success($this->translate('Check configuration has successfully been stored'));
        } else {
            return false;
        }
    }

    public function save()
    {
        foreach($this->getValues() as $key => $value) {
            if (strpos($key, 'value_') !== false) {
                $module = str_replace('value_', '', $key);
                $this->setGlobalCheckInterval($module, $value);
            }
            if (strpos($key, 'enabled_') !== false) {
                $module = str_replace('enabled_', '', $key);
                $this->setGlobalCheckEnabled($module, $value);
            }
        }

        return true;
    }

    protected function getAvailableModules()
    {
        $query = $this->db->select()
            ->from(
                'available_modules',
                array(
                    'name'
                )
            )->order(
                'name',
                'ASC'
            );

        return $query->fetchAll();
    }

    protected function getGlobalCheckConfiguration($module)
    {
        $query = $this->db->select()
            ->from(
                'global_module_checks',
                array(
                    'name',
                    'check_interval',
                    'enabled'
                )
            )->where(
                'name',
                $module
            );

        return $query->fetchRow();
    }

    protected function prepareGlobalCheckRow($module)
    {
        $module = strtolower($module);

        $query = $this->db->select()
            ->from(
                'global_module_checks',
                array(
                    'name',
                    'check_interval',
                    'enabled'
                )
            )->where(
                'name',
                $module
            );

        return $query->fetchRow();
    }

    protected function setGlobalCheckInterval($module, $value)
    {
        $row = $this->prepareGlobalCheckRow($module);

        if ($row == false) {
            $this->db->insert(
                'global_module_checks',
                array(
                    'name'           => $module,
                    'check_interval' => $value
                )
            );
        } else {
            if ($row->check_interval != $value) {
                $this->db->update(
                    'global_module_checks',
                    array(
                        'check_interval' => $value
                    ),
                    Filter::matchAll(
                        Filter::expression('name', '=', $module)
                    )
                );
            }
        }
        return true;
    }

    protected function setGlobalCheckEnabled($module, $active)
    {
        $row = $this->prepareGlobalCheckRow($module);

        if ($row == false) {
            $this->db->insert(
                'global_module_checks',
                array(
                    'name'     => $module,
                    'enabled'  => $active
                )
            );
        } else {
            if ($row->enabled != $active) {
                $this->db->update(
                    'global_module_checks',
                    array(
                        'enabled' => $active
                    ),
                    Filter::matchAll(
                        Filter::expression('name', '=', $module)
                    )
                );
            }
        }
        return true;
    }
}
