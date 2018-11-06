<?php

namespace Icinga\Module\Windows\Web\Widget;

class MemoryUsage extends UsageBar
{
    protected $formatter = [
        'Icinga\\Module\\Windows\\Format',
        'convertBytes'
    ];
}
