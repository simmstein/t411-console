<?php

namespace Transmission\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Api\ConfigLoader;

class TransmissionConfigureCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('transmission:configure')
            ->setDescription('')
            // ->addArgument('foo', InputArgument::OPTIONAL, '')
            // ->addOption('bar', null, InputOption::VALUE_NONE, '')
            ->setHelp("<info>%command.name%</info>

Configure your transmission web remote access.

Usage: <comment>transmission:configure</comment>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configLoader = new ConfigLoader();
        $dialog = $this->getHelperSet()->get('dialog');

        $host = $dialog->ask($output, 'Host (eg: https://seedox.example.com/): ', null);
        $endPoint = $dialog->ask($output, 'End point [/transmission/rpc]: ', null);
        $username = $dialog->ask($output, 'Username: ', null);
        $password = $dialog->askHiddenResponse($output, 'Password (hidden): ', null);

        $configLoader->save(array(
            'transmission' => array(
                'host' => $host,
                'endpoint' => $endPoint ? $endPoint : '/transmission/rpc',
                'username' => $username,
                'password' => $password,
            )
        ));
    }
}
