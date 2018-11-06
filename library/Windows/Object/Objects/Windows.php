<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class Windows extends BaseClass
{
    //use Properties;

    protected $hostname;

    protected $host_id;

    // Specific variables

    protected $caption;
    protected $buildNumber;
    protected $version;
    protected $systemDir;
    protected $rootDir;
    protected $systemDevice;
    protected $osLanguage;
    protected $architecture;
    protected $numberOfUsers;
    protected $osType;
    protected $numberOfLicensedUsers;
    protected $systemName;
    protected $buildType;
    protected $servicePackMajorVersion;
    protected $servicePackMinorVersion;
    protected $countryCode;
    protected $installDate;
    protected $registeredUser;
    protected $serialNumber;
    protected $systemDrive;
    protected $osSku;
    protected $localDatetime;
    protected $primary;
    protected $encryptionLevel;
    protected $dataExecutionPreventionAvailable;
    protected $description;
    protected $bootDevice;
    protected $manufacturer;
    protected $codeSet;
    protected $name;
    protected $languages;
    protected $lastBootTime;
    protected $locale;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadFromDB($fulldata = false)
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

        $queryColumns = array(
            'caption',
            'build_number',
            'version',
            'system_dir',
            'root_dir',
            'system_drive'
        );

        if ($fulldata) {
            array_push($queryColumns,
                'system_device',
                'os_language',
                'architecture',
                'number_of_users',
                'os_type',
                'number_of_licensed_users',
                'system_name',
                'build_type',
                'service_pack_major_version',
                'service_pack_minor_version',
                'country_code',
                'install_date',
                'registered_user',
                'serial_number',
                'system_drive',
                'os_sku',
                'local_datetime',
                'primary',
                'encryption_level',
                'data_execution_prevention_available',
                'description',
                'boot_device',
                'manufacturer',
                'code_set',
                'name',
                'languages',
                'last_boot_time',
                'locale'
            );
        }

        $query = $db->select()
            ->from(
                'host_system',
                $queryColumns
            )->where(
                'host_id',
                $this->host_id
            );

        $system = $query->fetchRow();

        if ($system == false) {
            return;
        }

        if (property_exists($system, 'system_device')) {
            $this->setSystemDevice($system->system_device);
        }

        if (property_exists($system, 'os_language')) {
            $this->setOsLanguage($system->os_language);
        }

        if (property_exists($system, 'architecture')) {
            $this->setArchitecture($system->architecture);
        }

        if (property_exists($system, 'number_of_users')) {
            $this->setNumberOfUsers($system->number_of_users);
        }

        if (property_exists($system, 'root_dir')) {
            $this->setRootDir($system->root_dir);
        }

        if (property_exists($system, 'system_dir')) {
            $this->setSystemDir($system->system_dir);
        }

        if (property_exists($system, 'os_type')) {
            $this->setOsType($system->os_type);
        }

        if (property_exists($system, 'number_of_licensed_users')) {
            $this->setNumberOfLicensedUsers($system->number_of_licensed_users);
        }

        if (property_exists($system, 'system_name')) {
            $this->setSystemName($system->system_name);
        }

        if (property_exists($system, 'build_type')) {
            $this->setBuildType($system->build_type);
        }

        if (property_exists($system, 'service_pack_major_version')) {
            $this->setServicePackMajorVersion($system->service_pack_major_version);
        }

        if (property_exists($system, 'service_pack_minor_version')) {
            $this->setServicePackMinorVersion($system->service_pack_minor_version);
        }

        if (property_exists($system, 'version')) {
            $this->setVersion($system->version);
        }

        if (property_exists($system, 'country_code')) {
            $this->setCountryCode($system->country_code);
        }

        if (property_exists($system, 'build_number')) {
            $this->setBuildNumber($system->build_number);
        }

        if (property_exists($system, 'install_date')) {
            $this->setInstallDate($system->install_date);
        }

        if (property_exists($system, 'registered_user')) {
            $this->setRegisteredUser($system->registered_user);
        }

        if (property_exists($system, 'serial_number')) {
            $this->setSerialNumber($system->serial_number);
        }

        if (property_exists($system, 'system_drive')) {
            $this->setSystemDrive($system->system_drive);
        }

        if (property_exists($system, 'os_sku')) {
            $this->setOsSku($system->os_sku);
        }

        if (property_exists($system, 'local_datetime')) {
            $this->setLocalDatetime($system->local_datetime);
        }

        if (property_exists($system, 'caption')) {
            $this->setCaption($system->caption);
        }

        if (property_exists($system, 'is_primary')) {
            $this->setPrimary($system->is_primary);
        }

        if (property_exists($system, 'encryption_level')) {
            $this->setEncryptionLevel($system->encryption_level);
        }

        if (property_exists($system, 'data_execution_prevention_available')) {
            $this->setDataExecutionPreventionAvailable($system->data_execution_prevention_available);
        }

        if (property_exists($system, 'description')) {
            $this->setDescription($system->description);
        }

        if (property_exists($system, 'boot_device')) {
            $this->setBootDevice($system->boot_device);
        }

        if (property_exists($system, 'manufacturer')) {
            $this->setManufacturer($system->manufacturer);
        }

        if (property_exists($system, 'code_set')) {
            $this->setCodeSet($system->code_set);
        }

        if (property_exists($system, 'name')) {
            $this->setName($system->name);
        }

        if (property_exists($system, 'languages')) {
            $this->setLanguages(explode(',', $system->languages));
        }

        if (property_exists($system, 'last_boot_time')) {
            $this->setLastBootTime($system->last_boot_time);
        }

        if (property_exists($system, 'locale')) {
            $this->setLocale($system->locale);
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
            'host_system',
            $deleteFilter
        );

        $system  = $content['output'];
        $primary = 0;
        $data_execution_prevention_available = 0;

        if (isset($system['Primary']) && $system['Primary'] !== null) {
            if ($system['Primary'] == true) {
                $primary = 1;
            }
        }

        if (isset($system['DataExecutionPrevention_Available']) && $system['DataExecutionPrevention_Available'] !== null) {
            if ($system['DataExecutionPrevention_Available'] == true) {
                $data_execution_prevention_available = 1;
            }
        }

        $db->insert(
            'host_system',
            array(
                'host_id'   => $this->host_id,
                'system_device' => (isset($system['SystemDevice']) && $system['SystemDevice'] !== null) ? $system['SystemDevice'] : '',
                'os_language' => (isset($system['OSLanguage']) && $system['OSLanguage'] !== null) ? $system['OSLanguage'] : 0,
                'architecture' => (isset($system['OSArchitecture']) && $system['OSArchitecture'] !== null) ? $system['OSArchitecture'] : '',
                'number_of_users' => (isset($system['NumberOfUsers']) && $system['NumberOfUsers'] !== null) ? $system['NumberOfUsers'] : 0,
                'root_dir' => (isset($system['WindowsDirectory']) && $system['WindowsDirectory'] !== null) ? $system['WindowsDirectory'] : '',
                'system_dir' => (isset($system['SystemDirectory']) && $system['SystemDirectory'] !== null) ? $system['SystemDirectory'] : '',
                'os_type' => (isset($system['OSType']) && $system['OSType'] !== null) ? $system['OSType'] : 0,
                'number_of_licensed_users' => (isset($system['NumberOfLicensedUsers']) && $system['NumberOfLicensedUsers'] !== null) ? $system['NumberOfLicensedUsers'] : 0,
                'system_name' => (isset($system['CSName']) && $system['CSName'] !== null) ? $system['CSName'] : '',
                'build_type' => (isset($system['BuildType']) && $system['BuildType'] !== null) ? $system['BuildType'] : '',
                'service_pack_major_version' => (isset($system['ServicePackMajorVersion']) && $system['ServicePackMajorVersion'] !== null) ? $system['ServicePackMajorVersion'] : 0,
                'service_pack_minor_version' => (isset($system['ServicePackMinorVersion']) && $system['ServicePackMinorVersion'] !== null) ? $system['ServicePackMinorVersion'] : 0,
                'version' => (isset($system['Version']) && $system['Version'] !== null) ? $system['Version'] : '',
                'country_code' => (isset($system['CountryCode']) && $system['CountryCode'] !== null) ? $system['CountryCode'] : '',
                'build_number' => (isset($system['BuildNumber']) && $system['BuildNumber'] !== null) ? $system['BuildNumber'] : '',
                'install_date' => (isset($system['InstallDate']) && $system['InstallDate'] !== null) ? $system['InstallDate'] : '',
                'registered_user' => (isset($system['RegisteredUser']) && $system['RegisteredUser'] !== null) ? $system['RegisteredUser'] : '',
                'serial_number' => (isset($system['SerialNumber']) && $system['SerialNumber'] !== null) ? $system['SerialNumber'] : '',
                'system_drive' => (isset($system['SystemDrive']) && $system['SystemDrive'] !== null) ? $system['SystemDrive'] : '',
                'os_sku' => (isset($system['OperatingSystemSKU']) && $system['OperatingSystemSKU'] !== null) ? $system['OperatingSystemSKU'] : 0,
                'local_datetime' => (isset($system['LocalDateTime']) && $system['LocalDateTime'] !== null) ? $system['LocalDateTime'] : '',
                'caption' => (isset($system['Caption']) && $system['Caption'] !== null) ? $system['Caption'] : '',
                'is_primary' => $primary,
                'encryption_level' => (isset($system['EncryptionLevel']) && $system['EncryptionLevel'] !== null) ? $system['EncryptionLevel'] : 0,
                'data_execution_prevention_available' => $data_execution_prevention_available,
                'description' => (isset($system['Description']) && $system['Description'] !== null) ? $system['Description'] : '',
                'boot_device' => (isset($system['BootDevice']) && $system['BootDevice'] !== null) ? $system['BootDevice'] : '',
                'manufacturer' => (isset($system['Manufacturer']) && $system['Manufacturer'] !== null) ? $system['Manufacturer'] : '',
                'code_set' => (isset($system['CodeSet']) && $system['CodeSet'] !== null) ? $system['CodeSet'] : '',
                'name' => (isset($system['Name']) && $system['Name'] !== null) ? $system['Name'] : '',
                'languages' => (isset($system['MUILanguages']) && $system['MUILanguages'] !== null) ? implode(',', $system['MUILanguages']) : '',
                'last_boot_time' => (isset($system['LastBootUpTime']) && $system['LastBootUpTime'] !== null) ? $system['LastBootUpTime'] : '',
                'locale' => (isset($system['Locale']) && $system['Locale'] !== null) ? $system['Locale'] : '',
            )
        );
    }

    /**
     * @return mixed
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param mixed $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return mixed
     */
    public function getBuildNumber()
    {
        return $this->buildNumber;
    }

    /**
     * @param mixed $buildNumber
     */
    public function setBuildNumber($buildNumber)
    {
        $this->buildNumber = $buildNumber;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getSystemDir()
    {
        return $this->systemDir;
    }

    /**
     * @param mixed $systemDir
     */
    public function setSystemDir($systemDir)
    {
        $this->systemDir = $systemDir;
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return mixed
     */
    public function getSystemDevice()
    {
        return $this->systemDevice;
    }

    /**
     * @param mixed $systemDevice
     */
    public function setSystemDevice($systemDevice)
    {
        $this->systemDevice = $systemDevice;
    }

    /**
     * @return mixed
     */
    public function getOsLanguage()
    {
        return $this->osLanguage;
    }

    /**
     * @param mixed $osLanguage
     */
    public function setOsLanguage($osLanguage)
    {
        $this->osLanguage = $osLanguage;
    }

    /**
     * @return mixed
     */
    public function getArchitecture()
    {
        return $this->architecture;
    }

    /**
     * @param mixed $architecture
     */
    public function setArchitecture($architecture)
    {
        $this->architecture = $architecture;
    }

    /**
     * @return mixed
     */
    public function getNumberOfUsers()
    {
        return $this->numberOfUsers;
    }

    /**
     * @param mixed $numberOfUsers
     */
    public function setNumberOfUsers($numberOfUsers)
    {
        $this->numberOfUsers = $numberOfUsers;
    }

    /**
     * @return mixed
     */
    public function getOsType()
    {
        return $this->osType;
    }

    /**
     * @param mixed $osType
     */
    public function setOsType($osType)
    {
        $this->osType = $osType;
    }

    /**
     * @return mixed
     */
    public function getNumberOfLicensedUsers()
    {
        return $this->numberOfLicensedUsers;
    }

    /**
     * @param mixed $numberOfLicensedUsers
     */
    public function setNumberOfLicensedUsers($numberOfLicensedUsers)
    {
        $this->numberOfLicensedUsers = $numberOfLicensedUsers;
    }

    /**
     * @return mixed
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @param mixed $systemName
     */
    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;
    }

    /**
     * @return mixed
     */
    public function getBuildType()
    {
        return $this->buildType;
    }

    /**
     * @param mixed $buildType
     */
    public function setBuildType($buildType)
    {
        $this->buildType = $buildType;
    }

    /**
     * @return mixed
     */
    public function getServicePackMajorVersion()
    {
        return $this->servicePackMajorVersion;
    }

    /**
     * @param mixed $servicePackMajorVersion
     */
    public function setServicePackMajorVersion($servicePackMajorVersion)
    {
        $this->servicePackMajorVersion = $servicePackMajorVersion;
    }

    /**
     * @return mixed
     */
    public function getServicePackMinorVersion()
    {
        return $this->servicePackMinorVersion;
    }

    /**
     * @param mixed $servicePackMinorVersion
     */
    public function setServicePackMinorVersion($servicePackMinorVersion)
    {
        $this->servicePackMinorVersion = $servicePackMinorVersion;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getInstallDate()
    {
        return $this->installDate;
    }

    /**
     * @param mixed $installDate
     */
    public function setInstallDate($installDate)
    {
        $this->installDate = $installDate;
    }

    /**
     * @return mixed
     */
    public function getRegisteredUser()
    {
        return $this->registeredUser;
    }

    /**
     * @param mixed $registeredUser
     */
    public function setRegisteredUser($registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return mixed
     */
    public function getSystemDrive()
    {
        return $this->systemDrive;
    }

    /**
     * @param mixed $systemDrive
     */
    public function setSystemDrive($systemDrive)
    {
        $this->systemDrive = $systemDrive;
    }

    /**
     * @return mixed
     */
    public function getOsSku()
    {
        return $this->osSku;
    }

    /**
     * @param mixed $osSku
     */
    public function setOsSku($osSku)
    {
        $this->osSku = $osSku;
    }

    /**
     * @return mixed
     */
    public function getLocalDatetime()
    {
        return $this->localDatetime;
    }

    /**
     * @param mixed $localDatetime
     */
    public function setLocalDatetime($localDatetime)
    {
        $this->localDatetime = $localDatetime;
    }

    /**
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @param mixed $primary
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;
    }

    /**
     * @return mixed
     */
    public function getEncryptionLevel()
    {
        return $this->encryptionLevel;
    }

    /**
     * @param mixed $encryptionLevel
     */
    public function setEncryptionLevel($encryptionLevel)
    {
        $this->encryptionLevel = $encryptionLevel;
    }

    /**
     * @return mixed
     */
    public function getDataExecutionPreventionAvailable()
    {
        return $this->dataExecutionPreventionAvailable;
    }

    /**
     * @param mixed $dataExecutionPreventionAvailable
     */
    public function setDataExecutionPreventionAvailable($dataExecutionPreventionAvailable)
    {
        $this->dataExecutionPreventionAvailable = $dataExecutionPreventionAvailable;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getBootDevice()
    {
        return $this->bootDevice;
    }

    /**
     * @param mixed $bootDevice
     */
    public function setBootDevice($bootDevice)
    {
        $this->bootDevice = $bootDevice;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getCodeSet()
    {
        return $this->codeSet;
    }

    /**
     * @param mixed $codeSet
     */
    public function setCodeSet($codeSet)
    {
        $this->codeSet = $codeSet;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param mixed $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return mixed
     */
    public function getLastBootTime()
    {
        return $this->lastBootTime;
    }

    /**
     * @param mixed $lastBootTime
     */
    public function setLastBootTime($lastBootTime)
    {
        $this->lastBootTime = $lastBootTime;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}