<?php

namespace Icinga\Module\Windows\Object\Objects;

use Icinga\Module\Windows\Helper\JsonParser;
use Icinga\Module\Windows\Helper\Properties;
use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;
use Icinga\Exception\ProgrammingError;

class Updates
{
    //use Properties;
    protected $hostname;

    protected $host_id;

    protected $installedUpdates = array();

    protected $uninstalledUpdates = array();

    protected $otherUpdates = array();

    protected $pendingUpdates = array();

    protected $hotfixes = array();

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    public function loadPendingUpdatesFromDB($updateName = null)
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
            'name',
            'kbarticles',
            'require_reboot',
            'downloaded'
        );

        if ($updateName != null) {
            array_push($queryColumns,
                'description',
                'uninst_note',
                'support_url',
                'download_size',
                'superseded_ids'
            );
        }

        $query = $db->select()
            ->from(
                'host_pending_updates',
                $queryColumns
            )->where(
                'host_id',
                $this->host_id
            );

        if ($updateName != null) {
            $query = $query->where(
                'name',
                $updateName
            );
        }

        $pendingUpdates = $query->fetchAll();
        $id = 0;

        foreach ($pendingUpdates as $update) {
            $pending = new Update();

            $pending->setName($update->name);
            if (property_exists($update, 'description')) {
                $pending->setDescription($update->description);
            }
            $pending->setKBArticles($update->kbarticles);
            if (property_exists($update, 'uninst_note')) {
                $pending->setUninstallNote($update->uninst_note);
            }
            if (property_exists($update, 'support_url')) {
                $pending->setSupportUrl($update->support_url);
            }
            $pending->setRequireReboot($update->require_reboot);
            if (property_exists($update, 'download_size')) {
                $pending->setDownloadSize($update->download_size);
            }
            $pending->setDownloaded($update->downloaded);
            if (property_exists($update, 'superseded_ids')) {
                $pending->setSupersededIDs($update->superseded_ids);
            }

            $this->pendingUpdates += array(
                $id => $pending
            );

            $id += 1;
        }

        if ($updateName != null) {
            return (isset($this->pendingUpdates[0]) ? $this->pendingUpdates[0] : null);
        }

        return $this->pendingUpdates;
    }

    public function loadUpdateHistoryFromDB($updateName = null, $updateDate = null)
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
            'name',
            'result',
            'installed_on',
            'internal_type'
        );

        if ($updateName != null && $updateDate != null) {
            array_push(
                $queryColumns,
                'description',
                'support_url'
            );
        }

        $query = $db->select()
            ->from(
                'host_update_history',
                $queryColumns
            )->where(
                'host_id',
                $this->host_id
            );

        if ($updateName != null && $updateDate != null) {
            $query = $query->where(
                'name',
                $updateName
            )->where(
                'installed_on',
                $updateDate
            );
        }

        $installedUpdates = $query->fetchAll();
        $id = 0;

        foreach ($installedUpdates as $update) {
            $installed = new Update();

            $installed->setName($update->name);
            if (property_exists($update, 'description')) {
                $installed->setDescription($update->description);
            }
            $installed->setUpdateResult($update->result);
            if (property_exists($update, 'support_url')) {
                $installed->setSupportUrl($update->support_url);
            }
            $installed->setInstalledOn($update->installed_on);
            $installed->setInternalType($update->internal_type);

            $this->installedUpdates += array(
                $id => $installed
            );

            $id += 1;
        }

        if ($updateName != null && $updateDate != null) {
            return (isset($this->installedUpdates[0]) ? $this->installedUpdates[0] : null);
        }

        return $this->installedUpdates;
    }

    public function loadHotfixesFromDB($updateName = null)
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

        $queryColumns =  array(
            'id',
            'description',
            'install_date'
        );

        if ($updateName != null) {
            array_push(
                $queryColumns,
                'name',
                'status',
                'support_url',
                'fix_comment',
                'service_pack',
                'installed_by'
            );
        }

        $query = $db->select()->from(
            'host_hotfix_history',
            $queryColumns
        )->where(
            'host_id',
            $this->host_id
        );

        if ($updateName != null) {
            $query->where(
                'id',
                $updateName
            );
        }

        $hotfixes = $query->fetchAll();
        $id = 0;

        foreach ($hotfixes as $hotfix) {
            $_hotfix = new Hotfix();

            $_hotfix->setId($hotfix->id);
            if (property_exists($hotfix, 'name')) {
                $_hotfix->setName($hotfix->name);
            }
            $_hotfix->setDescription($hotfix->description);
            if (property_exists($hotfix, 'status')) {
                $_hotfix->setStatus($hotfix->status);
            }
            $_hotfix->setInstallDate($hotfix->install_date);
            if (property_exists($hotfix, 'support_url')) {
                $_hotfix->setSupportUrl($hotfix->support_url);
            }
            if (property_exists($hotfix, 'fix_comment')) {
                $_hotfix->setFixComment($hotfix->fix_comment);
            }
            if (property_exists($hotfix, 'service_pack')) {
                $_hotfix->setServicePack($hotfix->service_pack);
            }
            if (property_exists($hotfix, 'installed_by')) {
                $_hotfix->setInstalledBy($hotfix->installed_by);
            }

            $this->hotfixes += array(
                $id => $_hotfix
            );

            $id += 1;
        }

        if ($updateName != null) {
            return (isset($this->hotfixes[0]) ? $this->hotfixes[0] : null);
        }

        return $this->hotfixes;
    }

    protected function fetchUpdateCategory($updates, $name)
    {
        if (isset($updates[$name]) === false) {
            return null;
        }

        $result = array();

        foreach ($updates[$name] as $index => $updateData) {

            $update = new Update();

            foreach ($updateData as $key => $value) {
                $this->json->setObjectDefaultValue(
                    $update,
                    $updateData,
                    $key
                );
            }

            $id = count($result) + 1;

            $result += array(
                $id => $update
            );
        }
        return $result;
    }

    public function getInstalledUpdates()
    {
        return $this->installedUpdates;
    }

    public function getInstalledUpdateById($id)
    {
        if (isset($this->installedUpdates[$id])) {
            return $this->installedUpdates[$id];
        }
        return null;
    }

    public function getPendingUpdates()
    {
        return $this->pendingUpdates;
    }

    public function getPendingUpdateById($id)
    {
        if (isset($this->pendingUpdates[$id])) {
            return $this->pendingUpdates[$id];
        }
        return null;
    }

    public function getUninstalledUpdates()
    {
        return $this->uninstalledUpdates;
    }

    public function getUninstalledUpdateById($id)
    {
        if (isset($this->uninstalledUpdates[$id])) {
            return $this->uninstalledUpdates[$id];
        }
        return null;
    }

    public function getOtherUpdates()
    {
        return $this->otherUpdates;
    }

    public function getOtherUpdateById($id)
    {
        if (isset($this->otherUpdates[$id])) {
            return $this->otherUpdates[$id];
        }
        return null;
    }

    public function getHotfixes()
    {
        return $this->hotfixes;
    }

    public function getHotfixById($id)
    {
        if (isset($this->hotfixes[$id])) {
            return $this->hotfixes[$id];
        }
        return null;
    }

    public function parseApiRequest($content)
    {
        if (isset($content['output']) == false) {
            return;
        }

        // Handle pending updates
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

        if (isset($content['output']['pending']) !== false) {
            $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
            $db->delete(
                'host_pending_updates',
                $deleteFilter
            );

            foreach ($content['output']['pending'] as $index => $pendingUpdate) {

                if (is_array($pendingUpdate) === false) {
                    continue;
                }

                $superseded_ids = '';
                $kbArticles = '';
                $downloaded = 0;
                $require_reboot = 0;

                if (isset($pendingUpdate['SupersededUpdateIDs']) && $pendingUpdate['SupersededUpdateIDs'] !== null) {
                    $superseded_ids = implode(", ", $pendingUpdate['SupersededUpdateIDs']);
                }

                if (isset($pendingUpdate['KBArticleIDs']) && $pendingUpdate['KBArticleIDs'] !== null) {
                    $kbArticles = implode(", ", $pendingUpdate['KBArticleIDs']);
                }

                if (isset($pendingUpdate['IsDownloaded']) && $pendingUpdate['IsDownloaded'] !== null) {
                    if ($pendingUpdate['IsDownloaded'] == true) {
                        $downloaded = 1;
                    }
                }

                if (isset($pendingUpdate['RebootRequired']) && $pendingUpdate['RebootRequired'] !== null) {
                    if ($pendingUpdate['RebootRequired'] == true) {
                        $require_reboot = 1;
                    }
                }

                $db->insert(
                    'host_pending_updates',
                    array(
                        'host_id'   => $this->host_id,
                        'name' => (isset($pendingUpdate['Title']) && $pendingUpdate['Title'] !== null) ? $pendingUpdate['Title'] : '',
                        'description' => (isset($pendingUpdate['Description']) && $pendingUpdate['Description'] !== null) ? $pendingUpdate['Description'] : '',
                        'kbarticles' => $kbArticles,
                        'uninst_note' => (isset($pendingUpdate['UninstallationNotes']) && $pendingUpdate['UninstallationNotes'] !== null) ? $pendingUpdate['UninstallationNotes'] : '',
                        'support_url' => (isset($pendingUpdate['SupportUrl']) && $pendingUpdate['SupportUrl'] !== null) ? $pendingUpdate['SupportUrl'] : '',
                        'require_reboot' => $require_reboot,
                        'download_size' => (isset($pendingUpdate['MaxDownloadSize']) && $pendingUpdate['MaxDownloadSize'] !== null) ? $pendingUpdate['MaxDownloadSize'] : 0,
                        'downloaded' => $downloaded,
                        'superseded_ids' => $superseded_ids
                    )
                );
            }
        }

        if (isset($content['output']['updates']) !== false) {
            if (isset($content['output']['updates']['installed']) !== false) {
                $deleteFilter = Filter::chain(
                    'AND',
                    array(
                        Filter::expression('host_id', '=', $this->host_id),
                        Filter::expression('internal_type', '=', 0)
                    )
                );
                $db->delete(
                    'host_update_history',
                    $deleteFilter
                );
            }

            foreach ($content['output']['updates']['installed'] as $index => $installedUpdate) {
                if (is_array($installedUpdate) === false) {
                    continue;
                }

                $db->insert(
                    'host_update_history',
                    array(
                        'host_id'   => $this->host_id,
                        'name' => (isset($installedUpdate['Title']) && $installedUpdate['Title'] !== null) ? $installedUpdate['Title'] : '',
                        'description' => (isset($installedUpdate['Description']) && $installedUpdate['Description'] !== null) ? $installedUpdate['Description'] : '',
                        'result' => (isset($installedUpdate['ResultCode']) && $installedUpdate['ResultCode'] !== null) ? $installedUpdate['ResultCode'] : 0,
                        'support_url' => (isset($installedUpdate['SupportUrl']) && $installedUpdate['SupportUrl'] !== null) ? $installedUpdate['SupportUrl'] : '',
                        'installed_on' => (isset($installedUpdate['Date']) && $installedUpdate['Date'] !== null) ? $installedUpdate['Date'] : '',
                        'internal_type' => 0
                    )
                );

                /*
                 * Note: Internal types are meant as follows:
                 * 0: Installed
                 * 1: Uninstalled
                 * 2: Other
                 *
                 * Todo: Implement uninstalled and other updates
                 */
            }
        }

        if (isset($content['output']['hotfixes']) !== false) {
            $deleteFilter = $updateFilter = Filter::expression('host_id', '=', $this->host_id);
            $db->delete(
                'host_hotfix_history',
                $deleteFilter
            );

            foreach ($content['output']['hotfixes'] as $index => $hotfix) {
                $db->insert(
                    'host_hotfix_history',
                    array(
                        'host_id'   => $this->host_id,
                        'id' => (isset($hotfix['HotFixID']) && $hotfix['HotFixID'] !== null) ? $hotfix['HotFixID'] : '',
                        'name' => (isset($hotfix['Name']) && $hotfix['Name'] !== null) ? $hotfix['Name'] : '',
                        'description' => (isset($hotfix['Description']) && $hotfix['Description'] !== null) ? $hotfix['Description'] : '',
                        'status' => (isset($hotfix['Status']) && $hotfix['Status'] !== null) ? $hotfix['Status'] : '',
                        'install_date' => (isset($hotfix['InstalledOn']) && $hotfix['InstalledOn'] !== null) ? $hotfix['InstalledOn'] : '',
                        'support_url' => (isset($hotfix['Caption']) && $hotfix['Caption'] !== null) ? $hotfix['Caption'] : '',
                        'fix_comment' => (isset($hotfix['FixComments']) && $hotfix['FixComments'] !== null) ? $hotfix['FixComments'] : '',
                        'service_pack' => (isset($hotfix['ServicePackInEffect']) && $hotfix['ServicePackInEffect'] !== null) ? $hotfix['ServicePackInEffect'] : '',
                        'installed_by' => (isset($hotfix['InstalledBy']) && $hotfix['InstalledBy'] !== null) ? $hotfix['InstalledBy'] : '',
                    )
                );
            }
        }
    }
}