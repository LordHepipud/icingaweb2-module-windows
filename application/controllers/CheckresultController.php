<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows\Controllers;

use Icinga\Web\Controller;
use Icinga\Module\Windows\CheckResultApi\ApiHandler;

/**
 * Documentation module index
 */
class CheckresultController extends Controller
{
    protected $requiresAuthentication = false;

    protected $response;

    protected $request;
    /**
     * Documentation module landing page
     *
     * Lists documentation links
     */
    public function init()
    {
        parent::init();
        $this->view->title = 'Windows Check Result parser';
    }

    public function indexAction()
    {
        $this->response = $this->getResponse();
        $this->request = $this->getRequest();

        if ($this->request->getMethod() !== 'POST') {
            $this->sendResponse(405);
            die();
        }

        if ($this->isApiCall() === false) {
            $this->sendResponse(403);
            die();
        }

        $this->parseApiRequest();
        die();
    }

    protected function isApiCall()
    {
        if ($this->request->getHeader('Content-Type') !== 'application/json') {
            return false;
        }

        if ($this->request->getHeader('Accept') !== 'application/json') {
            return false;
        }

        if ($this->request->getHeader('X-Windows-CheckResult') === false) {
            return false;
        }

        if ($this->request->getHeader('X-Windows-CheckResult') === '') {
            return false;
        }

        return true;
    }

    protected function parseApiRequest()
    {
        $data = @json_decode($this->request->getRawBody(), true);

        if ($data === null) {
            $this->sendResponse(406);
            die();
        }

        $token = $this->params->get('token');
        $results = $this->params->get('results');

        $api = new ApiHandler($data, $this->request, $token, $results);

        $this->sendResponse(
            $api->determineRequest(),
            $api->getResponse()
        );
    }

    protected function sendResponse($statusCode, $content = null)
    {
        $this->response->setHttpResponseCode($statusCode);

        if ($content !== null && $statusCode == 200) {
            echo json_encode($content, JSON_PRETTY_PRINT) . "\n";
        }

        $this->response->sendResponse();
    }
}