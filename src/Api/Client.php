<?php

namespace Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    const API_HOST = 'https://api.t411.me/';

    protected $client;

    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    public function getAuthorization($username, $password)
    {
        return new ClientResponse($this->get(
            'auth/',
            array(
                'username' => $username,
                'password' => $password,
            )
        );
    }

    public function get($uri, array $options = array())
    {
        try {
            return new ClientResponse($this->client->get(API_HOST.$uri, $options));
        } catch (RequestException $e) {
            throw new ApiClientException(sprintf('Request exception (GET): %s', $e->getMessage()));
        } catch (HttpConnectException $e) {
            throw new ApiClientException(sprintf('HTTP Connection exception: %s', $e->getMessage()));
        }
    }

    public function post($uri, array $options = array())
    {
        try {
            return new ClientResponse($this->client->post(API_HOST.$uri, $options));
        } catch (RequestException $e) {
            throw new ApiClientException(sprintf('Request exception (POST): %s', $e->getMessage()));
        } catch (HttpConnectException $e) {
            throw new ApiClientException(sprintf('HTTP Connection exception: %s', $e->getMessage()));
        }
    }
}
