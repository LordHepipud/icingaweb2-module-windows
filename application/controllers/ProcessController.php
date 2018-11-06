<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\WindowsController;
use Icinga\Module\Windows\Object\Objects\Processes;

/**
 * Documentation module index
 */
class ProcessController extends WindowsController
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
        $this->view->title = 'Windows ' . $this->translate(' Process') . ': ' . $this->translate('View');
        $this->assertPermission('windows/process');
    }

    public function indexAction()
    {
        $this->activateTab('processes');

        $this->view->host = $this->params->get('host');
        $this->view->port = $this->params->get('port');
        $this->view->process = $this->params->get('process');

        $processes = new Processes($this->view->host);
        $this->view->processContent = $processes->loadSingleDB($this->view->process);
    }
}