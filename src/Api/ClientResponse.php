<?php

namespace Api;

use GuzzleHttp\Message\Response;

class ClientResponse
{
    protected $response = null;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function hasError()
    {
        return isset($this->response->json()['error']);
    }

    public function getErrorCode()
    {
        if (!$this->hasError()) {
            return null;
        }

        return $this->response->json()['code'];
    }

    public function getErrorMessage()
    {
        if (!$this->hasError()) {
            return null;
        }

        return $this->response->json()['error'];
    }

    public function getData()
    {
        return $this->response->json();
    }

    public function getBody()
    {
        return $this->response->getBody();
    }
}
