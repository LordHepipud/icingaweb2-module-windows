<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use dipl\Translation\TranslationHelper;
use dipl\Web\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Helper\Tools;
use Icinga\Module\Windows\WindowsDB;

class CounterInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $category;

    protected $instance;

    protected $counter;

    protected $reference;

    protected $hostname;

    protected $host_id;

    public function __construct($category, $instance, $counter, $reference, $host)
    {
        $this->category = $category;
        $this->instance = $instance;
        $this->counter = $counter;
        $this->reference = $reference;
        $this->hostname = $host;

        $this->init();
    }

    protected function init()
    {
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    protected function getCounter()
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_perf_counter',
                array(
                    'category',
                    'instance',
                    'counter',
                    'raw_value',
                    'base_value',
                    'system_frequency',
                    'counter_frequency',
                    'counter_timestamp',
                    'timestamp',
                    'timestamp_100nsec',
                    'value',
                    'reference'
                )
            )->where(
                'host_id',
                $this->host_id
            );

        if ($this->reference != null) {
            $query->where('reference', $this->reference);
        } else {
            $query->where('category', $this->category);
            $query->where('instance', $this->instance);
        }

        $query->where('counter', $this->counter);

        return $query->fetchRow();
    }

    protected function getCounterHelp()
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_perf_counter_help',
                array(
                    'counter_type',
                    'type',
                    'help'
                )
            )->where(
                'counter',
                $this->counter
            );

        return $query->fetchRow();
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $counter_row = $this->getCounter();
        $counter_help = $this->getCounterHelp();

        $this->addNameValuePairs([
            $this->translate('Category') => $counter_row->category,
            $this->translate('Instance') => $counter_row->instance,
            $this->translate('Counter') => $counter_row->counter,
            $this->translate('Reference') => $counter_row->reference,
            $this->translate('Value') => $counter_row->value,
            $this->translate('Raw Value') => $counter_row->raw_value,
            $this->translate('Base Value') => $counter_row->base_value,
            $this->translate('System Frequency') => $counter_row->system_frequency,
            $this->translate('Counter Frequency') => $counter_row->counter_frequency,
            $this->translate('Counter Timestamp') => $counter_row->counter_timestamp,
            $this->translate('Timestamp') => $counter_row->timestamp,
            $this->translate('Timestamp 100 nsec') => $counter_row->timestamp_100nsec
        ]);

        if ($counter_help != null) {
            $this->addNameValuePairs([
                $this->translate('Counter Type') => ($counter_help->type . ' (' . $counter_help->counter_type . ')'),
                $this->translate('Description') => $counter_help->help
            ]);
        } else {
            $this->addNameValuePairs([
                $this->translate('Counter Type') => 'Not available',
                $this->translate('Description') => 'Not available'
            ]);
        }
    }
}
