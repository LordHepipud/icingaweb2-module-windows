<?php

namespace Icinga\Module\Windows\Web\Widget;

class InterfaceUsage extends UsageBar
{
    protected $formatter = [
        'Icinga\\Module\\Windows\\Format',
        'convertBytes'
    ];
}
