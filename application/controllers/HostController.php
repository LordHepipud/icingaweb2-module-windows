<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\BiosInfoTable;
use Icinga\Module\Windows\Web\Table\Object\DiskInfoTable;
use Icinga\Module\Windows\Web\Table\Object\NetworkInfoTable;
use Icinga\Module\Windows\Web\Table\Object\WindowsInfoTable;
use Icinga\Module\Windows\Web\Table\Object\ProcessorInfoTable;
use Icinga\Module\Windows\Web\Table\Object\MemoryInfoTable;

/**
 * Documentation module index
 */
class HostController extends Controller
{
    protected $response;
    /**
     * Documentation module landing page
     *
     * Lists documentation links
     */
    public function init()
    {
        $this->assertPermission('windows/host');
    }

    public function indexAction()
    {

        $this->addMainTabs('host');
        $host = $this->params->get('host');

        $this->addTitle($this->translate('Windows Host Details'));

        $this->content()->add(
            new WindowsInfoTable($host)
        );

        $this->content()->add(
            new BiosInfoTable($host)
        );

        $this->content()->add(
            new ProcessorInfoTable($host)
        );

        $this->content()->add(
            new MemoryInfoTable($host)
        );

        $this->content()->add(
            new DiskInfoTable($host)
        );

        $this->content()->add(
            new NetworkInfoTable($host)
        );
    }
}