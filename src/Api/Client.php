<?php

namespace Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    protected $client;

    protected $token;

    public function __construct()
    {
        $this->client = new GuzzleClient(array('base_url' => 'https://api.t411.me'));
    }

    public function getAuthorization($username, $password)
    {
        return $this->post(
            false,
            '/auth',
            array(
                'body' => array(
                    'username' => $username,
                    'password' => $password,
                ),
            )
        );
    }

    public function setAuthorization($token)
    {
        $this->token = $token;
    }

    public function getCategoriesTree()
    {
        return $this->get(true, '/categories/tree');
    }

    public function getTermsTree()
    {
        return $this->get(true, '/terms/tree');
    }

    public function get($needAuthorization, $uri, array $options = array())
    {
        return $this->send($needAuthorization, 'get', $uri, $options);
    }

    public function post($needAuthorization, $uri, array $options = array())
    {
        return $this->send($needAuthorization, 'post', $uri, $options);
    }

    protected function send($needAuthorization, $method, $uri, $options)
    {
        if ($needAuthorization) {
            $options = array_merge(
                $options,
                array(
                    'headers' => array(
                        'Authorization' => $this->token,
                    ),
                )
            );
        }

        try {
            return new ClientResponse($this->client->{$method}($uri, $options));
        } catch (RequestException $e) {
            throw new ApiClientException(sprintf('Request exception (%s): %s', strtoupper($method), $e->getMessage()));
        } catch (HttpConnectException $e) {
            throw new ApiClientException(sprintf('HTTP Connection exception: %s', $e->getMessage()));
        }
    }
}
