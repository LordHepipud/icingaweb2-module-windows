<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;

class Update
{
    protected $name;

    protected $description;

    protected $KBArticles;

    protected $uninstallNote;

    protected $supportUrl;

    protected $requireReboot;

    protected $downloadSize;

    protected $downloaded;

    protected $supersededIDs;

    protected $updateResult;

    protected $installedOn;

    protected $internalType;

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
    public function getKBArticles()
    {
        return $this->KBArticles;
    }

    /**
     * @param mixed $KBArticles
     */
    public function setKBArticles($KBArticles)
    {
        $this->KBArticles = $KBArticles;
    }

    /**
     * @return mixed
     */
    public function getUninstallNote()
    {
        return $this->uninstallNote;
    }

    /**
     * @param mixed $uninstallNote
     */
    public function setUninstallNote($uninstallNote)
    {
        $this->uninstallNote = $uninstallNote;
    }

    /**
     * @return mixed
     */
    public function getSupportUrl()
    {
        return $this->supportUrl;
    }

    /**
     * @param mixed $supportUrl
     */
    public function setSupportUrl($supportUrl)
    {
        $this->supportUrl = $supportUrl;
    }

    /**
     * @return mixed
     */
    public function getRequireReboot()
    {
        return $this->requireReboot;
    }

    /**
     * @param mixed $requireReboot
     */
    public function setRequireReboot($requireReboot)
    {
        $this->requireReboot = $requireReboot;
    }

    /**
     * @return mixed
     */
    public function getDownloadSize()
    {
        return $this->downloadSize;
    }

    /**
     * @param mixed $downloadSize
     */
    public function setDownloadSize($downloadSize)
    {
        $this->downloadSize = $downloadSize;
    }

    /**
     * @return mixed
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * @param mixed $downloaded
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;
    }

    /**
     * @return mixed
     */
    public function getSupersededIDs()
    {
        return $this->supersededIDs;
    }

    /**
     * @param mixed $supersededIDs
     */
    public function setSupersededIDs($supersededIDs)
    {
        $this->supersededIDs = $supersededIDs;
    }

    /**
     * @return mixed
     */
    public function getUpdateResult()
    {
        return $this->updateResult;
    }

    /**
     * @param mixed $updateResult
     */
    public function setUpdateResult($updateResult)
    {
        $this->updateResult = $updateResult;
    }

    /**
     * @return mixed
     */
    public function getInstalledOn()
    {
        return $this->installedOn;
    }

    /**
     * @param mixed $installedOn
     */
    public function setInstalledOn($installedOn)
    {
        $this->installedOn = $installedOn;
    }

    /**
     * @return mixed
     */
    public function getInternalType()
    {
        return $this->internalType;
    }

    /**
     * @param mixed $internalType
     */
    public function setInternalType($internalType)
    {
        $this->internalType = $internalType;
    }

    //Update data is allocated dynamicly over properties

    public function translateResultCode()
    {
        switch ($this->getUpdateResult()) {
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
            $this->ResultCode
        );
    }

    public function getResultCodeIcon()
    {
        switch ($this->getUpdateResult()) {
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