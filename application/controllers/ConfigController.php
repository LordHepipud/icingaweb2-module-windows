<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use dipl\Html\Html;
use Icinga\Module\Windows\Forms\Config\GlobalConfigForm;
use Icinga\Module\Windows\Web\Form\ApplyMigrationsForm;
use Icinga\Module\Windows\Web\Form\ChooseDbResourceForm;
use Icinga\Module\Windows\Db\Migrations;
use Icinga\Module\Windows\Controller;

class ConfigController extends Controller
{
    /**
     * @throws \Icinga\Security\SecurityException
     * @throws \Zend_Form_Exception
     */
    public function indexAction()
    {
        $this->assertPermission('windows/config');
        $this->addTitle($this->translate('Main Configuration'));
        $this->content()->add(
            ChooseDbResourceForm::load()->handleRequest()
        );

        if ($this->Config()->get('db', 'resource')) {
            $db = $this->db();

            $migrations = new Migrations($db);

            if ($migrations->hasSchema()) {
                if ($migrations->hasPendingMigrations()) {
                    $this->content()->add(
                        ApplyMigrationsForm::load()
                            ->setMigrations($migrations)
                            ->handleRequest()
                    );
                }
            } else {
                if ($migrations->hasModuleRelatedTable()) {
                    $this->content()->add(Html::tag('p', [
                        'class' => 'error'
                    ], $this->translate(
                        'The chosen Database resource contains related tables,'
                        . ' but the schema is not complete. In case you tried'
                        . ' a pre-release version of this module please drop'
                        . ' this database and start with a fresh new one.'
                    )));

                    return;
                } elseif ($migrations->hasAnyTable()) {
                    $this->content()->add(Html::tag('p', [
                        'class' => 'warning'
                    ], $this->translate(
                        'The chosen Database resource already contains tables. You'
                        . ' might want to continue with this DB resource, but we'
                        . ' strongly suggest to use an empty dedicated DB for this'
                        . ' module.'
                    )));
                }
                $this->content()->add(
                    ApplyMigrationsForm::load()
                        ->setMigrations($migrations)
                        ->handleRequest()
                );
            }

            $this->content()->add(
                Html::tag('h4', [],
                    $this->translate(
                        'Configure your global checks below'
                    )
                )
            );

            $this->content()->add(
                (new GlobalConfigForm($db))->handleRequest()
            );
        }
    }
}
