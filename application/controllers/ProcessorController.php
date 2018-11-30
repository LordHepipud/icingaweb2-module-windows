<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\ProcessorDetailsInfoTable;

/**
 * Documentation module index
 */
class ProcessorController extends Controller
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
        $this->assertPermission('windows/processor');
    }

    public function indexAction()
    {
        $this->addMainTabs('hosts');

        $host = $this->params->get('host');

        $this->addTitle($this->translate('Host Processor Details'));
        $this->content()->add(
            new ProcessorDetailsInfoTable($host)
        );
   }
}
