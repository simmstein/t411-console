<?php

namespace Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    protected $client;

    public function __construct()
    {
        $this->client = new GuzzleClient(array('base_url' => 'https://api.t411.me'));
    }

    public function getAuthorization($username, $password)
    {
        return $this->post(
            '/auth',
            array(
                'body' => array(
                    'username' => $username,
                    'password' => $password,
                ),
            )
        );
    }

    public function getCategoriesTree()
    {
        return $this->get('/categories/tree');
    }

    public function get($uri, array $options = array())
    {
        return $this->send('get', $uri, $options);
    }

    public function post($uri, array $options = array())
    {
        return $this->send('post', $uri, $options);
    }

    protected function send($method, $uri, $options)
    {
        try {
            return new ClientResponse($this->client->{$method}($uri, $options));
        } catch (RequestException $e) {
            throw new ApiClientException(sprintf('Request exception (%s): %s', strtoupper($method), $e->getMessage()));
        } catch (HttpConnectException $e) {
            throw new ApiClientException(sprintf('HTTP Connection exception: %s', $e->getMessage()));
        }
    }
}
