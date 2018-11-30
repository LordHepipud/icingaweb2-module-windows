<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\CountersTable;

/**
 * Documentation module index
 */
class CountersController extends Controller
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
        $this->assertPermission('windows/counters');
    }

    public function indexAction()
    {
        $this->addMainTabs('host');

        $reference = $this->params->get('reference');
        $this->addTitle($this->translate('Windows Counter Overview:') . ' ' . $reference . ':');
        $table = new CountersTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->setReference($reference);
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
   }
}
