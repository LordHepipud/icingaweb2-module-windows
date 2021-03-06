<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\HotfixTable;

/**
 * Documentation module index
 */
class HotfixesController extends Controller
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
        $this->assertPermission('windows/hotfixes');
    }

    /**
     * @throws \Icinga\Exception\ConfigurationError
     */
    public function indexAction()
    {
        $this->addMainTabs('hotfixes');

        $this->addTitle($this->translate('Installed Hotfixes Overview'));
        $table = new HotfixTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}