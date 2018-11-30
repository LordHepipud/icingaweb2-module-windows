<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\PendingUpdatesTable;

/**
 * Documentation module index
 */
class PendingUpdatesController extends Controller
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
        $this->assertPermission('windows/pendingupdates');
    }

    /**
     * @throws \Icinga\Exception\ConfigurationError
     */
    public function indexAction()
    {
        $this->addMainTabs('updates');

        $this->addTitle($this->translate('Pending Updates Overview'));
        $table = new PendingUpdatesTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}