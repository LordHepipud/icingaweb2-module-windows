<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Forms\Config\BackendConfigForm;
use Icinga\Module\Windows\Forms\Config\GlobalConfigForm;
use Icinga\Web\Controller;

class ConfigController extends Controller
{
    public function init()
    {
        $this->assertPermission('config/modules');
        parent::init();
    }

    public function indexAction()
    {
        $backendConfig = new BackendConfigForm();
        $backendConfig
            ->setIniConfig($this->Config())
            ->handleRequest();
        $this->view->backendConfig = $backendConfig;

        // TODO: Store global config within database
        $globalConfig = new GlobalConfigForm();
        $globalConfig
            ->setIniConfig($this->Config())
            ->handleRequest();
        $this->view->globalConfig = $globalConfig;

        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config');
    }
}
