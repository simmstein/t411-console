<?php

namespace Transmission\Client;

use Vohof\GuzzleClient as BaseGuzzleClient;

class GuzzleClient extends BaseGuzzleClient
{
    public function __construct($host, $options = array())
    {
        parent::__construct($host, $options);

        $this->client->setDefaultOption('verify', false);
    }
}
