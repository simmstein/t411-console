<?php

namespace Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Finder\Finder;

class Application extends BaseApplication
{
    protected $commandsPaths = array(
        'src/Console/Command' => 'Console\\Command',
    );

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->loadCommands();
    }

    public function addCommandsPath($path, $namespace)
    {
        $this->commandsPaths[$path] = trim($namespace, '/');

        return $this;
    }

    protected function loadCommands()
    {
        $finder = new Finder();

        foreach ($this->commandsPaths as $path => $namespace) {
            $finder->files('*Command.php')->in($path);

            foreach ($finder as $file) {
                $className = $namespace.'\\'.str_replace('.php', '', $file->getFilename());
                $this->addCommands(array(
                    new $className(),
                ));
            }
        }
    }

}
