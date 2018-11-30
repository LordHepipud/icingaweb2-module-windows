<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\InstalledUpdatesTable;

/**
 * Documentation module index
 */
class UpdateHistoryController extends Controller
{
    protected $response;

    /**
     * Documentation module landing page
     *
     * Lists documentation links
     * @throws \Icinga\Security\SecurityException
     */
    public function init()
    {
        $this->assertPermission('windows/updatehistor');
    }

    /**
     * @throws \Icinga\Exception\ConfigurationError
     */
    public function indexAction()
    {
        $this->addMainTabs('updates');

        $this->addTitle($this->translate('Updates Overview'));
        $table = new InstalledUpdatesTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}