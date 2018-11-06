<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\ServiceInfoTable;
use Icinga\Web\Url;

/**
 * Documentation module index
 */
class ServiceController extends Controller
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
        $this->assertPermission('windows/service');
    }

    public function indexAction()
    {
        $this->addMainTabs('services');

        $host = $this->params->get('host');
        $service = $this->params->get('service');

        $this->addTitle($this->translate('Windows Service Details:') . ' ' . $service);
        $this->content()->add(
            new ServiceInfoTable($service, $host)
        );
   }
}
