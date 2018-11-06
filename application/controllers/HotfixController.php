<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Object\Objects\Updates;
use Icinga\Module\Windows\WindowsController;

/**
 * Documentation module index
 */
class HotfixController extends WindowsController
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
        $this->assertPermission('windows/hotfix');
    }

    public function indexAction()
    {
        $this->activateTab('hotfixes');

        $this->view->host = $this->params->get('host');
        $updates = new Updates($this->view->host);

        $this->view->hotfix = $updates->loadHotfixesFromDB(
            $this->params->get('id')
        );
    }
}