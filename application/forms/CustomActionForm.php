<?php

namespace Icinga\Module\Windows\Forms;

use dipl\Html\Icon;
use Icinga\Module\Director\Web\Form\DirectorForm;

class CustomActionForm extends DirectorForm
{
    private $label;

    private $title;

    private $onSuccessAction;

    public function __construct($label, $title, $action, $params = [])
    {
        parent::__construct([
            'data-base-target' => '_self'
        ]);
        $this->label = $label;
        $this->title = $title;
        foreach ($params as $name => $value) {
            $this->addHidden($name, $value);
        }
        $this->setAction($action);
    }

    public function runOnSuccess($action)
    {
        $this->onSuccessAction = $action;

        return $this;
    }

    public function setup()
    {
        $this->setAttrib('class', 'inline');
        $this->addSubmitButton($this->label, [
            'class'            => 'link-button',
            'title'            => $this->title,
        ]);
    }

    public function onSuccess()
    {
        if ($this->onSuccessAction !== null) {
            $func = $this->onSuccessAction;
            $func();
            $this->redirectOnSuccess(
                $this->translate('Action has successfully been executed')
            );
        }
    }
}
