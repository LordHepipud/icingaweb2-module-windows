<?php

namespace Icinga\Module\Windows\Web\Table;

use dipl\Html\Html;
use dipl\Html\Icon;
use dipl\Html\Link;

class HostsTable extends BaseTable
{
    protected $searchColumns = [
        'host',
        'address',
        'os',
    ];

    protected function initialize()
    {
        $this->addAvailableColumns([
            $this->createColumn('host', $this->translate('Host'), [
                'host',
                'address'
            ])->setRenderer(function ($row) {
                $link = Link::create($row->host,'windows/host', [
                    'host'   => $row->host
                ], [
                    'icon'  => 'host',
                    'class' => 'action-link'
                ]);
                if ($row->host === $row->address . 'd') {
                    return $link;
                } else {
                    return [
                        $link,
                        ' ',
                        Html::tag('small', [
                            'style' => 'color: #999'
                        ], $row->address)
                    ];
                }
            }),
            $this->createColumn('os'),
            $this->createColumn('version'),
            $this->createColumn('approved', $this->translate('Approved'), 'approved')->setRenderer(function ($row) {
                if ((int) $row->approved === 1) {
                    return Link::create(
                        $this->translate(''),
                        'windows/hosts',
                        [
                            'action' => 'revoke',
                            'host' => $row->host
                        ],
                        [
                            'class' => 'icon-ok',
                            'data-base-target' => '_main'
                        ]
                    );
                } else {
                    return Link::create(
                        $this->translate(''),
                        'windows/hosts',
                        [
                            'action' => 'approve',
                            'host' => $row->host
                        ],
                        [
                            'class' => 'icon-cancel',
                            'data-base-target' => '_main'
                        ]
                    );
                }
            })
        ]);
    }

    public function renderRow($row)
    {
        $tr = parent::renderRow($row);
        if ((int) $row->approved === 0) {
            $tr->addAttributes([
                'class' => 'disabled'
            ]);
        }

        return $tr;
    }

    public function getDefaultColumnNames()
    {
        return [
            'host',
            'os',
            'version',
            'approved'
        ];
    }

    public function prepareQuery()
    {
        return $this->db()->select()->from('host_list', $this->getRequiredDbColumns() + ['approved' => 'approved']);
    }
}