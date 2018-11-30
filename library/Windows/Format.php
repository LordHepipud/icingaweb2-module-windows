<?php

namespace Icinga\Module\Windows;

class Format
{
    public static function convertBytes($bytes)
    {
        if ($bytes > 1099511627776) {
            return sprintf('%.2f TB', $bytes / 1099511627776);
        } elseif ($bytes > 1073741824) {
            return sprintf('%.2f GB', $bytes / 1073741824);
        } elseif ($bytes > 1048576) {
            return sprintf('%.2f MB', $bytes / 1048576);
        } else {
            return sprintf('%.2f KB', $bytes / 1024);
        }
    }
}
