<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\ServicesTable;

/**
 * Documentation module index
 */
class ServicesController extends Controller
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
        $this->assertPermission('windows/services');
    }

    public function indexAction()
    {
        $this->addMainTabs('services');

        $this->addTitle($this->translate('Service Overview'));
        $table = new ServicesTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}
