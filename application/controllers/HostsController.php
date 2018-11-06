<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use dipl\Html\Html;
use dipl\Html\Link;
use Icinga\Module\Windows\Forms\CustomActionForm;
use Icinga\Module\Windows\Controller;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Web\Table\HostsTable;
use Icinga\Web\Url;

/**
 * Documentation module index
 */
class HostsController extends Controller
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
        $this->assertPermission('windows/hosts');
    }

    public function indexAction()
    {
        $this->tabs()->add('hosts', array(
            'active'    => true,
            'title'     => $this->translate('Hosts', 'Tab title'),
            'url'       => Url::fromRequest()
        ));
        $this->tabs()->add('config', array(
            'active'    => false,
            'title'     => $this->translate('Config', 'Tab title'),
            'url'       => \Icinga\Web\Url::fromRequest()->setPath('windows/config')
        ));

        $this->addTitle($this->translate('Windows Hosts'));

        if (DbHelper::getInstance()->isDbConfigured() == false) {
            $this->content()->add(
                Html::tag(
                    'p',
                    null,
                    $this->translate('It seems you havent setup a database and/or the module schema yet. Please switch to config and apply the configuration there.')
                )
            );
            return;
        }

        $table = new HostsTable($this->getDb());
        $this->actions()->add([
            Link::create(
                $this->translate('Approve all'),
                'windows/hosts',
                ['action' => 'approve'],
                ['class' => 'icon-ok']
            ),
            Link::create(
                $this->translate('Revoke all'),
                'windows/hosts',
                ['action' => 'revoke'],
                ['class' => 'icon-cancel']
            ),
        ]);
        if ($this->params->get('action') === 'approve') {
            $this->showApproveForm();
        }
        if ($this->params->get('action') === 'revoke') {
            $this->showRevokeForm();
        }
        $table->handleSortUrl($this->url());
        $table->renderTo($this);
   }

    protected function showApproveForm()
    {
        $host = $this->params->get('host');
        $listUrl = $this->url()->without(['action', 'host']);
        $this->actions()->prepend(
           Link::create(
               $this->translate('back'),
               $listUrl,
               null,
               ['class' => 'icon-left-big']
           )
        );
        $form = new CustomActionForm(
           $this->translate('Approve'),
           $host == null ? $this->translate('This will approve all  pending Hosts') : ($this->translate('This will approve host ' . $host)),
           $this->url()
        );
        if ($host != null) {
           $this->addTitle('Approve Host %s', $host);
        }
        $this->content()->add(
            Html::tag('p', [
                'class' => 'warning'
            ], $host == null ? $this->translate('Are you sure you want to approve all pendings hosts?') : $this->translate('Are you sure you want to approve this host?'))
        );
        $form->runOnSuccess(function () {
           $host = $this->params->get('host');
           if ($host == null) {
               $this->setAllHostsApprovalStatus(1);
           } else {
               $this->setHostApproved($host, 1);
           }
           $this->params->remove('host');
        })->setSuccessUrl($listUrl)->handleRequest();
        $this->content()->add($form);
   }

   protected function showRevokeForm()
   {
       $host = $this->params->get('host');
       $listUrl = $this->url()->without(['action', 'host']);
       $this->actions()->prepend(
           Link::create(
               $this->translate('back'),
               $listUrl,
               null,
               ['class' => 'icon-left-big']
           )
       );
       $form = new CustomActionForm(
           $this->translate('Revoke now'),
           $host == null ? $this->translate('This will revoke all Hosts') : ($this->translate('This will revoke host ' . $host)),
           $this->url()
       );
       if ($host != null) {
           $this->addTitle('Revoke Host %s', $host);
       }
       $this->content()->add(
           Html::tag('p', [
               'class' => 'warning'
           ], $host == null ? $this->translate('Be careful, this will revoke all current hosts and prevent them from sending system information.') : $this->translate('Be careful, this will revoke the host and prevent him from sending data.'))
       );
       $form->runOnSuccess(function () {
           $host = $this->params->get('host');
           if ($host == null) {
               $this->setAllHostsApprovalStatus(0);
           } else {
               $this->setHostApproved($host, 0);
           }
       })->setSuccessUrl($listUrl)->handleRequest();
       $this->content()->add($form);
   }
}
