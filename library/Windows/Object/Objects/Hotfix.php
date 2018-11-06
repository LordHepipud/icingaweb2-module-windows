<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\Properties;

class Hotfix
{

    protected $id;

    protected $name;

    protected $description;

    protected $status;

    protected $installDate;

    protected $supportUrl;

    protected $fixComment;

    protected $servicePack;

    protected $installedBy;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getFixComment()
    {
        return $this->fixComment;
    }

    /**
     * @param mixed $fixComment
     */
    public function setFixComment($fixComment)
    {
        $this->fixComment = $fixComment;
    }

    /**
     * @return mixed
     */
    public function getServicePack()
    {
        return $this->servicePack;
    }

    /**
     * @param mixed $servicePack
     */
    public function setServicePack($servicePack)
    {
        $this->servicePack = $servicePack;
    }

    /**
     * @return mixed
     */
    public function getInstalledBy()
    {
        return $this->installedBy;
    }

    /**
     * @param mixed $installedOn
     */
    public function setInstalledBy($installedBy)
    {
        $this->installedBy = $installedBy;
    }
}