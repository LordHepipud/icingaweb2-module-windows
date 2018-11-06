<?php

namespace Icinga\Module\Windows\Helper;

use Icinga\Application\Benchmark;
use Icinga\Exception\ConfigurationError;
use Exception;

class JsonParser
{
    protected $input;

    public function __construct($object)
    {
        $this->input = $object;
    }

    public function getCounterValue($counter)
    {
        $data = $this->getCounter($counter);

        if (isset($data['value'])) {
            return $data['value'];
        }

        return $data;
    }

    public function getCounterHelp($counter)
    {
        $data = $this->getCounter($counter);

        if (isset($data['help'])) {
            return $data['help'];
        }

        return $data;
    }

    public function getCounterRawValue($counter)
    {
        $data = $this->getCounter($counter);

        if (isset($data['sample']['RawValue'])) {
            return $data['sample']['RawValue'];
        }

        return $data;
    }

    public function getCounter($counter)
    {
        if (isset($this->input[$counter])) {
            return $this->input[$counter];
        }

        return null;
    }

    public function getCounterArray($counter)
    {
        if (isset($this->input[$counter])) {
            $data = $this->input[$counter];

            if (is_array($data) === false) {
                return null;
            }

            return $data;
        }

        return null;
    }

    public function setObjectDefaultValue($object, $array, $name)
    {
        if ($object === null || $array === null) {
            return;
        }

        if (is_array($name)) {
            foreach ($name as $key) {
                if (isset($array[$key])) {
                    $object->$key = $array[$key];
                } else {
                    $object->$key = null;
                }
            }
        } else {
            if (isset($array[$name])) {
                $object->$name = $array[$name];
            } else {
                $object->$name = null;
            }
        }
    }
}