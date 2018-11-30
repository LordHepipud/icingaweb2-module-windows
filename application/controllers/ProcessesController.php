<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\ProcessesTable;

/**
 * Documentation module index
 */
class ProcessesController extends Controller
{
    protected $response;
    /**
     * Documentation module landing page
     *
     * Lists documentation links
     */
    public function init()
    {
        $this->assertPermission('windows/processes');
    }

    public function indexAction()
    {
        $this->addMainTabs('processes');

        $this->addTitle($this->translate('Process Overview'));
        $table = new ProcessesTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}