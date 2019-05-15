<?php

namespace Icinga\Module\Windows\Web\Table\Object;

use gipfl\Translation\TranslationHelper;
use gipfl\IcingaWeb2\Link;
use ipl\Html\Html;
use gipfl\IcingaWeb2\Widget\NameValueTable;
use Icinga\Module\Windows\Helper\DbHelper;
use Icinga\Module\Windows\Object\Objects\Services;

class ServiceInfoTable extends NameValueTable
{
    use TranslationHelper;

    protected $services;

    protected $service;

    protected $hostname;

    protected $host_id;

    public function __construct($service, $host)
    {
        $this->service = $service;
        $this->hostname = $host;
        $this->services = new Services($host);

        $this->init();
    }

    protected function init()
    {
        $this->host_id = DbHelper::getInstance()->getHostIdByName($this->hostname);
    }

    /**
     * @throws \Icinga\Exception\NotFoundError
     */
    protected function assemble()
    {
        $service = $this->services->getService($this->service, true);

        $this->addNameValuePairs([
            $this->translate('Display Name') => $service->display_name,
            $this->translate('Name') => $service->service_name,
            $this->translate('Status') => $this->services->getServiceStatus($service->status),
            $this->translate('Start Type') => $service->start_type,
            $this->translate('Can Pause and Continue') => $service->can_pause_and_continue,
            $this->translate('Service Handle') => $service->service_handle,
            $this->translate('Can Stop') => $service->can_stop,
            $this->translate('Can Shutdown') => $service->can_shutdown,
            $this->translate('Service Type') => $service->service_type,
        ]);

        $dependentServices = explode(',', $service->dependent_services);
        $this->addNameValueRow(
            Html::tag('strong', $this->translate('Dependent Services')), ''
        );

        if (count($dependentServices) != 0) {
            foreach ($dependentServices as $_service) {
                if ($_service == '') {
                    continue;
                }
                $row = $this->services->getService($_service, false);
                if ($row == false) {
                    $this->addNameValueRow(
                        $_service, 'Service is not listed. Unknown status'
                    );
                    continue;
                }

                $this->addNameValueRow(
                    $this->createServiceLink($row->display_name, $row->service_name),
                    $this->services->getServiceStatus($row->status)
                );
            }
        }

        $dependsOnServices = explode(',', $service->depends_on);
        $this->addNameValueRow(
            Html::tag('strong', $this->translate('Dependens On')), ''
        );
        if (count($dependsOnServices) != 0) {
            foreach ($dependsOnServices as $_service) {
                if ($_service == '') {
                    continue;
                }
                $row = $this->services->getService($_service, false);
                if ($row == false) {
                    $this->addNameValuePairs([
                        $_service => 'Service is not listed. Unknown status'
                    ]);
                    continue;
                }
                $this->addNameValueRow(
                    $this->createServiceLink($row->display_name, $row->service_name),
                    $this->services->getServiceStatus($row->status)
                );
            }
        }
    }

    protected function createServiceLink($name, $service)
    {
        return Link::create(
            $name . ' (' . $service . ')',
            'windows/service',
            [
                'service' => $service,
                'host' => $this->hostname
            ]
        );
    }
}
