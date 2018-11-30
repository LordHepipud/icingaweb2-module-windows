<?php

namespace Icinga\Module\Windows\Object\Objects;

class Process
{
    protected $processId;

    protected $name;

    protected $threadCount;

    protected $priority;

    protected $cmdline;

    protected $pagefile;

    protected $description;

    protected $caption;

    protected $virtualSize;

    protected $processorPercent = 0;

    protected $processorTime = 0;

    protected $kernelModeTime;

    protected $userModeTime;

    protected $usedMemory;

    protected $workingSetSize;


    protected $binaryPath;

    /**
     * @return mixed
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param mixed $processId
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
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
    public function getThreadCount()
    {
        return $this->threadCount;
    }

    /**
     * @param mixed $threadCount
     */
    public function setThreadCount($threadCount)
    {
        $this->threadCount = $threadCount;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getCMDLine()
    {
        return $this->cmdline;
    }

    /**
     * @param mixed $cmdline
     */
    public function setCMDLine($cmdline)
    {
        $this->cmdline = $cmdline;
    }

    /**
     * @return mixed
     */
    public function getPagefile()
    {
        return $this->pagefile;
    }

    /**
     * @param mixed $pagefile
     */
    public function setPagefile($pagefile)
    {
        $this->pagefile = $pagefile;
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
    public function getVirtualSize()
    {
        return $this->virtualSize;
    }

    /**
     * @param mixed $virtualSize
     */
    public function setVirtualSize($virtualSize)
    {
        $this->virtualSize = $virtualSize;
    }

    /**
     * @return mixed
     */
    public function getKernelModeTime()
    {
        return $this->kernelModeTime;
    }

    /**
     * @param mixed $kernelModeTime
     */
    public function setKernelModeTime($kernelModeTime)
    {
        $this->kernelModeTime = $kernelModeTime;
    }

    /**
     * @return mixed
     */
    public function getUserModeTime()
    {
        return $this->userModeTime;
    }

    /**
     * @param mixed $userModeTime
     */
    public function setUserModeTime($userModeTime)
    {
        $this->userModeTime = $userModeTime;
    }

    /**
     * @return mixed
     */
    public function getWorkingSetSize()
    {
        return $this->workingSetSize;
    }

    /**
     * @param mixed $workingSetSize
     */
    public function setWorkingSetSize($workingSetSize)
    {
        $this->workingSetSize = $workingSetSize;
    }

    /**
     * @return mixed
     */
    public function getBinaryPath()
    {
        return $this->binaryPath;
    }

    /**
     * @param mixed $binaryPath
     */
    public function setBinaryPath($binaryPath)
    {
        $this->binaryPath = $binaryPath;
    }

    public function setProcessProcessorTime($processorTime)
    {
        $this->processorTime = $processorTime;
    }

    /**
     * @return int
     */
    public function getProcessorPercent()
    {
        return $this->processorPercent;
    }

    /**
     * @param int $processorPercent
     */
    public function setProcessorPercent($processorPercent)
    {
        $this->processorPercent = $processorPercent;
    }

    /**
     * @return mixed
     */
    public function getUsedMemory()
    {
        return $this->usedMemory;
    }

    /**
     * @param mixed $usedMemory
     */
    public function setUsedMemory($usedMemory)
    {
        $this->usedMemory = $usedMemory;
    }



    public function getProcessProcessorTime($in_seconds)
    {
        $value = $this->processorTime;
        if ($value == 0) {
            $value = ($this->getKernelModeTime() + $this->getUserModeTime());
        }

        if ($in_seconds == true) {
            return ($value / 10000000);
        }

        return $value;
    }
}