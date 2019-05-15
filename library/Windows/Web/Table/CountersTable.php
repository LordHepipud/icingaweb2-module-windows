<?php

namespace Icinga\Module\Windows\Web\Table;

use gipfl\IcingaWeb2\Link;
use Icinga\Module\Windows\Format;
use Icinga\Module\Windows\Helper\DbHelper;

class CountersTable extends BaseTable
{
    protected $hostname;

    protected $reference;

    protected $counter;

    protected $category;

    protected $instance;

    protected $searchColumns = [
        'counter',
        'reference',
        'value'
    ];

    public function setHost($hostname)
    {
        $this->hostname = $hostname;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    protected function initialize()
    {
        if ($this->reference !== null) {
            $this->addAvailableColumns([
                $this->createColumn('counter', $this->translate('Counter'), [
                    'counter',
                    'reference'
                ])->setRenderer(function ($row) {
                    return Link::create($row->counter, 'windows/counter', [
                        'counter' => $row->counter,
                        'host' => $this->hostname,
                        'reference' => $row->reference
                    ], [
                        'icon' => 'chart-area',
                        'class' => 'action-link'
                    ]);
                }),
                $this->createColumn('value', $this->translate('Value'))
                    ->setRenderer(function ($row) {
                        if (strpos($row->counter, 'yte') === false) {
                            return sprintf('%.2F', $row->value);
                        } else {
                            return Format::convertBytes($row->value);
                        }
                    }),
            ]);
        } else {
            $this->addAvailableColumns([
                $this->createColumn('counter', $this->translate('Counter'), [
                    'category',
                    'counter',
                    'instance'
                ])->setRenderer(function ($row) {
                    return Link::create($row->counter, 'windows/counter', [
                        'counter' => $row->counter,
                        'host' => $this->hostname,
                        'category' => $row->category,
                        'instance' => $row->instance
                    ], [
                        'icon' => 'chart-area',
                        'class' => 'action-link'
                    ]);
                }),
                $this->createColumn('instance', $this->translate('Instance')),
                $this->createColumn('category', $this->translate('Category')),
                $this->createColumn('value', $this->translate('Value')),
            ]);
        }
    }

    public function getDefaultColumnNames()
    {
        return [
            'counter',
            'reference',
            'category',
            'instance',
            'value'
        ];
    }

    public function prepareQuery()
    {
        $host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
        $db = $this->db()->select()->from('host_perf_counter', $this->getRequiredDbColumns())
            ->where('host_id = ?', $host_id);

        if ($this->reference !== null) {
            $db->where('reference = ?', $this->reference);
        }

        if ($this->category !== null) {
            $db->where('category = ?', $this->category);
        }

        if ($this->instance !== null) {
            $db->where('instance = ?', $this->instance);
        }

        return $db;
    }
}