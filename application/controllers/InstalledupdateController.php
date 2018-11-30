<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\InstalledUpdateInfoTable;

/**
 * Documentation module index
 */
class InstalledupdateController extends Controller
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
        $this->assertPermission('windows/installedupdate');
    }

    public function indexAction()
    {
        $this->addMainTabs('updates');

        $this->addTitle($this->translate('Installed Update Details'));

        $this->content()->add(
            new InstalledUpdateInfoTable(
                $this->params->get('host'),
                $this->params->get('name'),
                $this->params->get('date')
            )
        );
    }
}