<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\HotfixInfoTable;

/**
 * Documentation module index
 */
class HotfixController extends Controller
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
        $this->assertPermission('windows/hotfix');
    }

    public function indexAction()
    {
        $this->addMainTabs('hotfixes');

        $this->addTitle($this->translate('Hotfix Details'));

        $this->content()->add(
            new HotfixInfoTable(
                $this->params->get('host'),
                $this->params->get('id')
            )
        );
    }
}