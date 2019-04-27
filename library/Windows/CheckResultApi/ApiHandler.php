<?php

namespace Icinga\Module\Windows\CheckResultApi;

use Icinga\Module\Windows\WindowsDB;
use Icinga\Data\Filter\Filter;

class ApiHandler
{
    protected $json = null;

    protected $token = null;

    protected $receiveResults = null;

    protected $hostId = null;

    protected $request    = null;

    protected $response   = null;

    public function __construct($json, $request, $token, $results)
    {
        $this->json    = $json;
        $this->request = $request;
        $this->token   = $token;
        $this->receiveResults = $results;
    }

    public function determineRequest()
    {
         if ($this->request->getHeader('X-Windows-Hello') == '1') {
             return $this->parseWindowsHello();
         } else {
             if ($this->request->getHeader('X-Windows-Result') == '1') {
                 if ($this->parseResults() == true) {
                     return 200;
                 }

                 return 401;
             }
         }

         return 404;
    }

    protected function parseWindowsHello()
    {
        if ($this->receiveResults == 1) {
            return 200;
        }
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from('host_list', array('host', 'approved', 'host_id'))
            ->where('host', $this->json['fqdn']);

        $host = $query->fetchRow();

        if ($host == false) {
            $db->insert(
                'host_list',
                array(
                    'host'    => $this->json['fqdn'],
                    'address' => $this->json['fqdn'],
                    'os'      => $this->json['os'],
                    'version' => $this->json['version'],
                    'port'    => $this->json['port']
                )
            );

            $query = $db->select()
                ->from('host_list', array('host_id'))
                ->where('host', $this->json['fqdn']);

            $host = $query->fetchRow();

            $db->insert(
                'host_token_list',
                array(
                    'host_id' => $host->host_id,
                    'token'   => ''
                )
            );

            return 401;
        }

        $db->update(
            'host_list',
            array(
                'os'      => $this->json['os'],
                'version' => $this->json['version'],
                'port'    => $this->json['port']
            ),
            Filter::matchAll(
                Filter::expression('host', '=', $this->json['fqdn'])
            )
        );

        if ($host->approved == 0) {
            return 401;
        }

        $this->hostId = $host->host_id;

        $this->parseAvailableModules();
        // This call must the last, because we are refreshing the token here
        $this->sendHostConfiguration();

        return 200;
    }

    protected function sendHostConfiguration()
    {
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from('global_module_checks', array('name', 'check_interval'))
            ->where('enabled', 1);

        $modules = $query->fetchAll();
        $authToken = $this->generateAuthToken();

        $query = $db->select()
            ->from(
                'host_token_list',
                array('token')
            )->where(
                'host_id',
                $this->hostId
            );

        $row = $query->fetchRow();

        if ($row == false) {
            $db->insert(
                'host_token_list',
                array(
                    'host_id' => $this->hostId,
                    'token'   => $authToken
                )
            );
        } else {
            $db->update(
                'host_token_list',
                array(
                    'token' => $authToken
                ),
                Filter::matchAll(
                    Filter::expression('host_id', '=', $this->hostId)
                )
            );
        }

        $this->response = array(
            'modules' => $modules,
            'token'   => $authToken,
            'module_arguments' => null
        );
    }

    protected function generateAuthToken()
    {
        $seed = mt_rand();
        $token = hash('sha256', self::getSessionId() . $seed);

        return sprintf('%s|%s', $seed, $token);
    }

    /**
     * Get current session id
     *
     * TODO: we should do this through our App or Session object
     *
     * @return string
     */
    protected static function getSessionId()
    {
        return session_id();
    }

    protected function isTokenValid()
    {
        if ($this->token == null || $this->token == '') {
            return false;
        }

        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from('host_token_list', array('token', 'created'))
            ->where('host_id', $this->hostId);

        $storedToken = $query->fetchRow();

        if ($storedToken == false) {
            return false;
        }

        if ($this->token != $storedToken->token) {
            return false;
        }

        $tokenTime = strtotime($storedToken->created);
        $currentTime = time();

        // Tokens are only valid for 60 seconds
        if(($currentTime-$tokenTime) > 60) {
            return false;
        }

        return true;
    }

    protected function parseAvailableModules()
    {
        if ($this->isTokenValid() == false) {
            return;
        }

        $db = WindowsDB::fromConfig();

        $modulelist = $db
            ->select()
            ->from(
                'available_modules',
                array(
                    'name'
                )
            )->fetchAll();

        $missingModules = array();

        foreach ($this->json['modules'] as $json) {
            $moduleFound = false;

            foreach ($modulelist as $module) {
                if (strtolower($module->name) == strtolower($json)) {
                    $moduleFound = true;
                    break;
                }
            }

            if ($moduleFound == false) {
                $missingModules += array(
                    $json => strtolower($json)
                );
            }
        }

        foreach ($missingModules as $module) {
            $db->insert(
                'available_modules',
                array(
                    'name' => $module
                )
            );
        }
    }

    protected function parseResults()
    {
        if ($this->receiveResults == 0 || $this->receiveResults == null) {
            return false;
        }

        $hostname = $this->request->getHeader('X-Windows-CheckResult');
        $db = WindowsDB::fromConfig();

        $query = $db->select()
            ->from('host_list', array('host', 'approved', 'host_id'))
            ->where('host', $hostname);

        $host = $query->fetchRow();
        $this->hostId = $host->host_id;

        if ($host->approved == 0) {
            return false;
        }

        if ($this->isTokenValid() == false) {
            return false;
        }

        foreach ($this->json as $key => $output) {
            /*
             * $class = "\\Icinga\\Module\\Windows\\Object\\Objects\\" . $key;
             * $object = new $class($output); /**@var $object BaseClass
             */

            $className = '';

            switch ($key) {
                case 'process':
                    $className = 'Processes';
                    break;
                case 'updates':
                    $className = 'Updates';
                    break;
                case 'hardware':
                    $className = 'Hardware';
                    break;
                case 'bios':
                    $className = 'Bios';
                    break;
                case 'windows':
                    $className = 'Windows';
                    break;
                case 'cpu':
                    $className = 'Cpu';
                    break;
                case 'memory':
                    $className = 'Memory';
                    break;
                case 'disk':
                    $className = 'Disks';
                    break;
                case 'network':
                    $className = 'Network';
                    break;
                case 'services':
                    $className = 'Services';
                    break;
                default:
                    $this->writePlainJsonToDb($key, $output);
                    continue 2;
            }

            $class = "\\Icinga\\Module\\Windows\\Object\\Objects\\" . $className;
            if (! class_exists($class)) {
                continue;
            }

            $object = new $class($hostname);

            $object->parseApiRequest($output);
        }

        $this->response = array(
            'response' => 200,
            'msg'      => 'All modules have been transmitted'
        );

        return true;
    }

    protected function writePlainJsonToDb($module, $output)
    {
        $db = WindowsDB::fromConfig();

        $deleteFilter = Filter::chain(
            'AND',
            array(
                Filter::expression('host_id', '=', $this->hostId),
                Filter::expression('module', '=', $module)
            )
        );

        $db->delete(
            'host_check_results',
            $deleteFilter
        );

        $db->insert(
            'host_check_results',
            array(
                'host_id' => $this->hostId,
                'module'  => $module,
                'result'  => json_encode($output['output'])
            )
        );
    }

    public function getResponse()
    {
        return $this->response;
    }
}