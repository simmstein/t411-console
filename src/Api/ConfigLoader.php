<?php

namespace Api;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{
    protected $configFile = 'app/config.yml';

    protected $filesystem = null;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function configExists()
    {
        return $this->filesystem->exists($this->configFile);
    }

    public function save(array $data)
    {
        $config = array_merge(
            $this->getConfig(),
            $data
        );

        $this->filesystem->dumpFile($this->configFile, Yaml::dump($config));
    }

    public function getConfig()
    {
        return $this->configExists() ? Yaml::parse($this->configFile) : array();
    }
}
