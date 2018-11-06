<?php

namespace Icinga\Module\Windows\Core;

use Icinga\Application\Benchmark;
use Icinga\Exception\ConfigurationError;
use Exception;

class RestApiClient
{
    protected $version = 'v1';
    protected $peer;
    protected $port;
    protected $curl;

    public function __construct($peer, $port = 5891, $cn = null)
    {
        $this->peer = $peer;
        $this->port = $port;
    }

    public function getPeerIdentity()
    {
        return $this->peer;
    }

    public function request($method, $url)
    {
        if (function_exists('curl_version')) {
            return $this->curlRequest($method, $url);
            /*
            // Completely disabled fallback method, caused too many issues
            // with hanging connections on specific PHP versions
            } elseif (version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // TODO: fail if stream
                return $this->phpRequest($method, $url, $body, $raw);
            */
        } else {
            throw new Exception(
                'No CURL extension detected, it must be installed and enabled'
            );
        }
    }

    protected function url($url)
    {
        return sprintf('https://%s:%d/%s/%s', $this->peer, $this->port, $this->version, $url);
    }

    /**
     * @throws Exception
     *
     * @return resource
     */
    protected function curl()
    {
        if ($this->curl === null) {
            $this->curl = curl_init(sprintf('https://%s:%d', $this->peer, $this->port));
            if (! $this->curl) {
                throw new Exception('CURL INIT ERROR: ' . curl_error($this->curl));
            }
        }

        return $this->curl;
    }

    protected function curlRequest($method, $url)
    {
        $headers = array(
            'Host: ' . $this->getPeerIdentity(),
            'Connection: close',
            'Accept: application/json'
        );

        $curl = $this->curl();
        $opts = array(
            CURLOPT_URL            => $this->url($url),
            CURLOPT_HTTPHEADER     => $headers,
            //CURLOPT_USERPWD        => $auth,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,

            // TODO: Fix this!
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        curl_setopt_array($curl, $opts);

        $res = curl_exec($curl);
        if ($res === false) {
            throw new Exception('CURL ERROR: ' . curl_error($curl));
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode === 401) {
            throw new ConfigurationError(
                'Unable to authenticate, please check your API credentials'
            );
        }

        return @json_decode($res, true);
    }

}