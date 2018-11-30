<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\UpdatesInfoTable;

/**
 * Documentation module index
 */
class UpdatesController extends Controller
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
        $this->assertPermission('windows/updates');
    }

    public function indexAction()
    {
        $this->addMainTabs('updates');

        $this->addTitle($this->translate('Updates Overview'));
        $this->content()->add(
            new UpdatesInfoTable($this->params->get('host'))
        );
    }
}