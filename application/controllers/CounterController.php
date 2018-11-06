<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Web\Table\Object\CounterInfoTable;
use Icinga\Web\Url;

/**
 * Documentation module index
 */
class CounterController extends Controller
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
        $this->assertPermission('windows/counter');
    }

    public function indexAction()
    {
        $this->addMainTabs('host');

        $host = $this->params->get('host');
        $category = $this->params->get('category');
        $instance = $this->params->get('instance');
        $counter = $this->params->get('counter');
        $reference = $this->params->get('reference');
        $name = '';
        if ($category != null && $instance != null) {
            if ($instance != null) {
                $name = '\\' . $category . '(' . $instance . ')\\' . $counter;
            } else {
                $name = '\\' . $category . '\\' . $counter;
            }
        } else {
            $name = $reference . '\\' . $counter;
        }

        $this->addTitle($this->translate('Windows Counter Details:') . ' ' . $name . ':');
        $this->content()->add(
            new CounterInfoTable($category, $instance, $counter, $reference, $host)
        );
   }
}
