<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Bios extends BaseClass
{
    //use Properties;

    protected $hostname;

    protected $host_id;

    protected $bios_data = array();

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadAllFromDB($caption = null, $description = null, $serial = null)
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_list',
                array('host_id')
            )->where(
                'host',
                $this->hostname
            );

        $host = $query->fetchRow();
        $this->host_id = $host->host_id;

        $query = $db->select()
            ->from(
                'host_bios',
                array(
                    'embedded_controller_major_version',
                    'embedded_controller_minor_version',
                    'description',
                    'software_element_state',
                    'smbios_major_version',
                    'smbios_minor_version',
                    'bios_characteristics',
                    'system_bios_major_version',
                    'system_bios_minor_version',
                    'version',
                    'smbios_version',
                    'primary_bios',
                    'smbios_present',
                    'current_language',
                    'available_languages',
                    'installable_languages',
                    'status',
                    'caption',
                    'release_date',
                    'manufacturer',
                    'software_element_id',
                    'name',
                    'serial_number',
                    'bios_version',
                )
            )->where(
                'host_id',
                $this->host_id
            );

        if ($caption != null && $description != null && $serial != null) {
            $query->where(
                'serial_number',
                $serial
            )->where(
                'description',
                $description
            )->where(
                'caption',
                $caption
            );
        }

        $result = $query->fetchAll();

        foreach ($result as $_bios) {
            $bios = new BiosData();

            if (property_exists($_bios, 'embedded_controller_major_version')) {
                $bios->setEmbeddedControllerMajorVersion($_bios->embedded_controller_major_version);
            }

            if (property_exists($_bios, 'embedded_controller_minor_version')) {
                $bios->setEmbeddedControllerMinorVersion($_bios->embedded_controller_minor_version);
            }

            if (property_exists($_bios, 'description')) {
                $bios->setDescription($_bios->description);
            }

            if (property_exists($_bios, 'software_element_state')) {
                $bios->setSoftwareElementState($_bios->software_element_state);
            }

            if (property_exists($_bios, 'smbios_major_version')) {
                $bios->setSmbiosMajorVersion($_bios->smbios_major_version);
            }

            if (property_exists($_bios, 'smbios_minor_version')) {
                $bios->setSmbiosMinorVersion($_bios->smbios_minor_version);
            }

            if (property_exists($_bios, 'bios_characteristics')) {
                $bios->setBiosCharacteristics(explode(',', $_bios->bios_characteristics));
            }

            if (property_exists($_bios, 'system_bios_major_version')) {
                $bios->setSystemBiosMajorVersion($_bios->system_bios_major_version);
            }

            if (property_exists($_bios, 'system_bios_minor_version')) {
                $bios->setSystemBiosMinorVersion($_bios->system_bios_minor_version);
            }

            if (property_exists($_bios, 'version')) {
                $bios->setVersion($_bios->version);
            }

            if (property_exists($_bios, 'smbios_version')) {
                $bios->setSmbiosVersion($_bios->smbios_version);
            }

            if (property_exists($_bios, 'primary_bios')) {
                $bios->setPrimaryBios($_bios->primary_bios);
            }

            if (property_exists($_bios, 'smbios_present')) {
                $bios->setSmbiosPresent($_bios->smbios_present);
            }

            if (property_exists($_bios, 'current_language')) {
                $bios->setCurrentLanguage($_bios->current_language);
            }

            if (property_exists($_bios, 'available_languages')) {
                $bios->setAvailableLanguages(explode(',', $_bios->available_languages));
            }

            if (property_exists($_bios, 'installable_languages')) {
                $bios->setInstallableLanguages($_bios->installable_languages);
            }

            if (property_exists($_bios, 'status')) {
                $bios->setStatus($_bios->status);
            }

            if (property_exists($_bios, 'caption')) {
                $bios->setCaption($_bios->caption);
            }

            if (property_exists($_bios, 'release_date')) {
                $bios->setReleaseDate($_bios->release_date);
            }

            if (property_exists($_bios, 'manufacturer')) {
                $bios->setManufacturer($_bios->manufacturer);
            }

            if (property_exists($_bios, 'software_element_id')) {
                $bios->setSoftwareElementId($_bios->software_element_id);
            }

            if (property_exists($_bios, 'name')) {
                $bios->setName($_bios->name);
            }

            if (property_exists($_bios, 'serial_number')) {
                $bios->setSerialNumber($_bios->serial_number);
            }

            if (property_exists($_bios, 'bios_version')) {
                $bios->setBiosVersion($_bios->bios_version);
            }

            array_push(
                $this->bios_data,
                $bios
            );
        }
    }

    public function parseApiRequest($content)
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from(
                'host_list',
                array('host_id')
            )->where(
                'host',
                $this->hostname
            );

        $host = $query->fetchRow();
        $this->host_id = $host->host_id;

        $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
        $db->delete(
            'host_bios',
            $deleteFilter
        );

        $bios  = $content['output'];
        //foreach ($content['output'] as $index => $bios) {
            $smbios_present = 0;
            $primary_bios = 0;

            if (isset($bios['SMBIOSPresent']) && $bios['SMBIOSPresent'] !== null) {
                if ($bios['SMBIOSPresent'] == true) {
                    $smbios_present = 1;
                }
            }

            if (isset($bios['PrimaryBIOS']) && $bios['PrimaryBIOS'] !== null) {
                if ($bios['PrimaryBIOS'] == true) {
                    $primary_bios = 1;
                }
            }

            $db->insert(
                'host_bios',
                array(
                    'host_id'   => $this->host_id,
                    'embedded_controller_major_version' => (isset($bios['EmbeddedControllerMajorVersion']) && $bios['EmbeddedControllerMajorVersion'] !== null) ? $bios['EmbeddedControllerMajorVersion'] : 0,
                    'embedded_controller_minor_version' => (isset($bios['EmbeddedControllerMinorVersion']) && $bios['EmbeddedControllerMinorVersion'] !== null) ? $bios['EmbeddedControllerMinorVersion'] : 0,
                    'description' => (isset($bios['Description']) && $bios['Description'] !== null) ? $bios['Description'] : '',
                    'software_element_state' => (isset($bios['SoftwareElementState']) && $bios['SoftwareElementState'] !== null) ? $bios['SoftwareElementState'] : 0,
                    'smbios_major_version' => (isset($bios['SMBIOSMajorVersion']) && $bios['SMBIOSMajorVersion'] !== null) ? $bios['SMBIOSMajorVersion'] : 0,
                    'smbios_minor_version' => (isset($bios['SystemBiosMinorVersion']) && $bios['SystemBiosMinorVersion'] !== null) ? $bios['SystemBiosMinorVersion'] : 0,
                    'bios_characteristics' => (isset($bios['BiosCharacteristics']) && $bios['BiosCharacteristics'] !== null) ? implode(',', $bios['BiosCharacteristics']) : '',
                    'system_bios_major_version' => (isset($bios['SystemBiosMajorVersion']) && $bios['SystemBiosMajorVersion'] !== null) ? $bios['SystemBiosMajorVersion'] : 0,
                    'system_bios_minor_version' => (isset($bios['SystemBiosMinorVersion']) && $bios['SystemBiosMinorVersion'] !== null) ? $bios['SystemBiosMinorVersion'] : 0,
                    'version' => (isset($bios['Version']) && $bios['Version'] !== null) ? $bios['Version'] : '',
                    'smbios_version' => (isset($bios['SMBIOSBIOSVersion']) && $bios['SMBIOSBIOSVersion'] !== null) ? $bios['SMBIOSBIOSVersion'] : '',
                    'primary_bios' => $primary_bios,
                    'smbios_present' => $smbios_present,
                    'current_language' => (isset($bios['CurrentLanguage']) && $bios['CurrentLanguage'] !== null) ? $bios['CurrentLanguage'] : '',
                    'available_languages' => (isset($bios['ListOfLanguages']) && $bios['ListOfLanguages'] !== null) ? implode(',', $bios['ListOfLanguages']) : '',
                    'installable_languages' => (isset($bios['InstallableLanguages']) && $bios['InstallableLanguages'] !== null) ? $bios['InstallableLanguages'] : 0,
                    'status' => (isset($bios['Status']) && $bios['Status'] !== null) ? $bios['Status'] : '',
                    'caption' => (isset($bios['Caption']) && $bios['Caption'] !== null) ? $bios['Caption'] : '',
                    'release_date' => (isset($bios['ReleaseDate']) && $bios['ReleaseDate'] !== null) ? $bios['ReleaseDate'] : '',
                    'manufacturer' => (isset($bios['Manufacturer']) && $bios['Manufacturer'] !== null) ? $bios['Manufacturer'] : '',
                    'software_element_id' => (isset($bios['SoftwareElementID']) && $bios['SoftwareElementID'] !== null) ? $bios['SoftwareElementID'] : '',
                    'name' => (isset($bios['Name']) && $bios['Name'] !== null) ? $bios['Name'] : '',
                    'bios_version' => (isset($bios['BIOSVersion']) && $bios['BIOSVersion'] !== null) ? implode(',', $bios['BIOSVersion']) : '',
                    'serial_number' => (isset($bios['SerialNumber']) && $bios['SerialNumber'] !== null) ? $bios['SerialNumber'] : ''
                )
            );
        //}
    }

    public function getBiosData()
    {
        return $this->bios_data;
    }
}