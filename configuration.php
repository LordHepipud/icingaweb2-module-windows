<?php
/* Icinga Web 2 | (c) 2014 Icinga Development Team | GPLv2+ */

/** @var $this \Icinga\Application\Modules\Module */

$section = $this->menuSection(N_('Windows Infrastructure'), array(
    'title'    => 'Windows',
    'icon'     => 'sitemap',
    'url'      => 'windows',
    'priority' => 700
));

$section->add('Hosts', array(
    'url' => 'windows/hosts',
));

$this->provideConfigTab('hosts', array(
    'title' => $this->translate('Hosts'),
    'label' => $this->translate('Hosts'),
    'url' => 'hosts',
    'active' => false
));

$this->provideConfigTab('config', array(
    'title' => $this->translate('Configure Windows DB'),
    'label' => $this->translate('Config'),
    'url' => 'config',
    'active' => false
));

$this->provideSearchUrl($this->translate('Windows'), 'windows/search', -10);