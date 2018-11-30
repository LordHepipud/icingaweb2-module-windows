<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\PendingUpdateInfoTable;
/**
 * Documentation module index
 */
class PendingupdateController extends Controller
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
        $this->assertPermission('windows/pendingupdate');
    }

    public function indexAction()
    {
        $this->addMainTabs('updates');

        $this->addTitle($this->translate('Pending Update Details'));

        $this->content()->add(
            new PendingUpdateInfoTable(
                $this->params->get('host'),
                $this->params->get('name')
            )
        );
    }
}