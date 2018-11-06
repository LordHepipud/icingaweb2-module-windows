<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;

class BiosData
{

    protected $embeddedControllerMajorVersion;

    protected $embeddedControllerMinorVersion;

    protected $description;

    protected $softwareElementState;

    protected $smbiosMajorVersion;

    protected $smbiosMinorVersion;

    protected $biosCharacteristics;

    protected $systemBiosMajorVersion;

    protected $systemBiosMinorVersion;

    protected $version;

    protected $smbiosVersion;

    protected $primaryBios;

    protected $smbiosPresent;

    protected $currentLanguage;

    protected $availableLanguages;

    protected $installableLanguages;

    protected $status;

    protected $caption;

    protected $releaseDate;

    protected $manufacturer;

    protected $softwareElementId;

    protected $name;

    protected $serialNumber;

    protected $biosVersion;

    /**
     * @return mixed
     */
    public function getEmbeddedControllerMajorVersion()
    {
        return $this->embeddedControllerMajorVersion;
    }

    /**
     * @param mixed $embeddedControllerMajorVersion
     */
    public function setEmbeddedControllerMajorVersion($embeddedControllerMajorVersion)
    {
        $this->embeddedControllerMajorVersion = $embeddedControllerMajorVersion;
    }

    /**
     * @return mixed
     */
    public function getEmbeddedControllerMinorVersion()
    {
        return $this->embeddedControllerMinorVersion;
    }

    /**
     * @param mixed $embeddedControllerMinorVersion
     */
    public function setEmbeddedControllerMinorVersion($embeddedControllerMinorVersion)
    {
        $this->embeddedControllerMinorVersion = $embeddedControllerMinorVersion;
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
    public function getSoftwareElementState()
    {
        return $this->softwareElementState;
    }

    /**
     * @param mixed $softwareElementState
     */
    public function setSoftwareElementState($softwareElementState)
    {
        $this->softwareElementState = $softwareElementState;
    }

    /**
     * @return mixed
     */
    public function getSmbiosMajorVersion()
    {
        return $this->smbiosMajorVersion;
    }

    /**
     * @param mixed $smbiosMajorVersion
     */
    public function setSmbiosMajorVersion($smbiosMajorVersion)
    {
        $this->smbiosMajorVersion = $smbiosMajorVersion;
    }

    /**
     * @return mixed
     */
    public function getSmbiosMinorVersion()
    {
        return $this->smbiosMinorVersion;
    }

    /**
     * @param mixed $smbiosMinorVersion
     */
    public function setSmbiosMinorVersion($smbiosMinorVersion)
    {
        $this->smbiosMinorVersion = $smbiosMinorVersion;
    }

    /**
     * @return mixed
     */
    public function getBiosCharacteristics()
    {
        return $this->biosCharacteristics;
    }

    /**
     * @param mixed $biosCharacteristics
     */
    public function setBiosCharacteristics($biosCharacteristics)
    {
        $this->biosCharacteristics = $biosCharacteristics;
    }

    /**
     * @return mixed
     */
    public function getSystemBiosMajorVersion()
    {
        return $this->systemBiosMajorVersion;
    }

    /**
     * @param mixed $systemBiosMajorVersion
     */
    public function setSystemBiosMajorVersion($systemBiosMajorVersion)
    {
        $this->systemBiosMajorVersion = $systemBiosMajorVersion;
    }

    /**
     * @return mixed
     */
    public function getSystemBiosMinorVersion()
    {
        return $this->systemBiosMinorVersion;
    }

    /**
     * @param mixed $systemBiosMinorVersion
     */
    public function setSystemBiosMinorVersion($systemBiosMinorVersion)
    {
        $this->systemBiosMinorVersion = $systemBiosMinorVersion;
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
    public function getSmbiosVersion()
    {
        return $this->smbiosVersion;
    }

    /**
     * @param mixed $smbiosVersion
     */
    public function setSmbiosVersion($smbiosVersion)
    {
        $this->smbiosVersion = $smbiosVersion;
    }

    /**
     * @return mixed
     */
    public function getPrimaryBios()
    {
        return $this->primaryBios;
    }

    /**
     * @param mixed $primaryBios
     */
    public function setPrimaryBios($primaryBios)
    {
        $this->primaryBios = $primaryBios;
    }

    /**
     * @return mixed
     */
    public function getSmbiosPresent()
    {
        return $this->smbiosPresent;
    }

    /**
     * @param mixed $smbiosPresent
     */
    public function setSmbiosPresent($smbiosPresent)
    {
        $this->smbiosPresent = $smbiosPresent;
    }

    /**
     * @return mixed
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * @param mixed $currentLanguage
     */
    public function setCurrentLanguage($currentLanguage)
    {
        $this->currentLanguage = $currentLanguage;
    }

    /**
     * @return mixed
     */
    public function getAvailableLanguages()
    {
        return $this->availableLanguages;
    }

    /**
     * @param mixed $availableLanguages
     */
    public function setAvailableLanguages($availableLanguages)
    {
        $this->availableLanguages = $availableLanguages;
    }

    /**
     * @return mixed
     */
    public function getInstallableLanguages()
    {
        return $this->installableLanguages;
    }

    /**
     * @param mixed $installableLanguages
     */
    public function setInstallableLanguages($installableLanguages)
    {
        $this->installableLanguages = $installableLanguages;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @param mixed $releaseDate
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;
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
    public function getSoftwareElementId()
    {
        return $this->softwareElementId;
    }

    /**
     * @param mixed $softwareElementId
     */
    public function setSoftwareElementId($softwareElementId)
    {
        $this->softwareElementId = $softwareElementId;
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
    public function getBiosVersion()
    {
        return $this->biosVersion;
    }

    /**
     * @param mixed $biosVersion
     */
    public function setBiosVersion($biosVersion)
    {
        $this->biosVersion = $biosVersion;
    }
}