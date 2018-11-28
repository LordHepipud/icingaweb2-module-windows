<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\ProcessTable;

/**
 * Documentation module index
 */
class ProcessController extends Controller
{
    protected $response;
    /**
     * Documentation module landing page
     *
     * Lists documentation links
     */
    public function init()
    {
        $this->assertPermission('windows/process');
    }

    public function indexAction()
    {
        $this->addMainTabs('processes');

        $this->addTitle($this->translate('Process Details'));
        $table = new ProcessTable($this->getDb());
        $table->setHost($this->params->get('host'));
        $table->setProcess($this->params->get('process'));
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
    }
}