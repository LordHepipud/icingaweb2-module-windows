<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Object\Objects\Updates;
use Icinga\Module\Windows\Object\WindowsHost;
use Icinga\Module\Windows\WindowsController;
use Icinga\Module\Windows\Data\HostApi;
use Icinga\Web\Url;

/**
 * Documentation module index
 */
class UpdatesController extends WindowsController
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
        $this->assertPermission('windows/updates');
    }

    public function indexAction()
    {
        $this->activateTab('updates');

        $this->view->host = $this->params->get('host');
        $updates = new Updates($this->view->host);

        $this->view->pendingUpdates = $updates->loadPendingUpdatesFromDB();
        $this->view->updateHistory = $updates->loadUpdateHistoryFromDB();
    }
}