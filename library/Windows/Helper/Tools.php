<?php

namespace Icinga\Module\Windows\Helper;

use Icinga\Module\Windows\Web\Widget\SimpleUsageBar;

class Tools
{
    private static $instance = null;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function convertBytesToMB($value)
    {
        if ($value === null) {
            return $value;
        }

        if (is_numeric($value) === false) {
            return $value;
        }

        return ($value / 1024 / 1024);
    }

    public function convertMBToBytes($value)
    {
        if ($value === null) {
            return $value;
        }

        if (is_numeric($value) === false) {
            return $value;
        }

        return ($value * 1024 * 1024);
    }

    public function convertKBToMB($value)
    {
        if ($value === null) {
            return $value;
        }

        if (is_numeric($value) === false) {
            return $value;
        }

        return ($value / 1024);
    }

    public function getDateFromCounter($date)
    {
        return (date() - date(
            "l jS \of F Y h:i:s A",
            $date/10000000-11644473600
        ));
    }

    public function getDateFromWindows($date)
    {
        return date(
            "l jS \of F Y h:i:s A",
            $date/10000000-11644473600
        );
    }

    public function getDateFromPSOutput($date)
    {
        $date = str_replace('/Date(', '', $date);
        $date = str_replace(')/', '', $date);

        if (is_numeric($date) === false) {
            return $date;
        }

        $date = $date / 1000;

        return date(
            'l jS \of F Y h:i:s A',
            $date
        );
    }

    public function getOutputStatusByExitCode($exitcode)
    {
        switch($exitcode) {
            case 0:
                return 'OK:';
            case 1:
                return 'WARNING:';
            case 2:
                return 'CRITICAL:';
        }

        return 'UNKNOWN:';
    }

    public function getCliOutputMessage($message, $exitcode = 0)
    {
        printf("%s %s\n",
            $this->getOutputStatusByExitCode($exitcode),
            $message
        );
    }

    public function translateUpdateResultCode($code)
    {
        switch ($code) {
            case 1:
                return 'In Progress';
            case 2:
                return 'Success';
            case 3:
                return 'Success with Errors';
            case 4:
                return 'Failed';
            case 5:
                return 'Aborted';
        }

        return sprintf(
            '%s (%s)',
            'Unknown',
            $code
        );
    }

    public function getUpdateResultCodeIcon($code)
    {
        switch ($code) {
            case 1:
                return 'spinner';
            case 2:
                return 'ok';
            case 3:
                return 'warning-empty';
            case 4:
                return 'attention-circled';
            case 5:
                return 'clock';
        }

        return 'help';
    }
}