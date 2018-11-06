<?php

namespace Icinga\Module\Windows\Helper;

use Icinga\Web\Controller;

class Navigation
{
    private static $instance = null;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addBackButton($view, $url, $args = array())
    {
        $args += array(
            'host' => \Icinga\Web\Url::fromRequest()->getParam('host'),
            'port' => \Icinga\Web\Url::fromRequest()->getParam('port')
        );
        return $view->qlink(
            'back',
            $url,
            $args,
            array(
                'icon' => 'left-big',
                'class' => 'action-link'
            )
        );
    }
}