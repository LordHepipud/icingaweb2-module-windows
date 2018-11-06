<?php

namespace Icinga\Module\Windows\Web\Widget;

class DiskUsage extends UsageBar
{
    protected $formatter = [
        'Icinga\\Module\\Windows\\Format',
        'convertBytes'
    ];
}
