<?php

namespace Api;

use Symfony\Component\Filesystem\Filesystem;

class ConfigLoader
{
    protected $configFile = 'app/config.yml';

    protected $filesystem = null;

    protected function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function configExists()
    {
        return $this->filesystem->exists($this->configFile);
    }
}
