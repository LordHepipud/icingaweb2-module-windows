<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Object\Objects\Processes;
use Icinga\Module\Windows\Object\WindowsHost;
use Icinga\Module\Windows\WindowsController;
use Icinga\Module\Windows\Data\HostApi;
use Icinga\Web\Url;

/**
 * Documentation module index
 */
class ProcessesController extends WindowsController
{
    protected $response;
    /**
     * Documentation module landing page
     *
     * Lists documentation links
     */
    public function init()
    {
        parent::init();
        $this->view->title = 'Windows ' . $this->translate(' Environment') . ': ' . $this->translate('Overview');
        $this->assertPermission('windows/processes');
    }

    public function indexAction()
    {
        $this->activateTab('processes');

        $this->view->host = $this->params->get('host');

        $this->view->processList = new Processes($this->view->host);
        $this->view->processList->loadAllDB();
    }
}