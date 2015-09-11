<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Symfony\Component\Console\Input\InputOption;
use Api\ClientException;
use Helper\Formater;

class UsersProfileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users:profile')
            ->setDescription('Show a user\'s profile')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'The user id')
            ->setHelp("<info>%command.name%</info>

Show a user's profile (default is the auhentificated user). 

Usage: <comment>users:profile</comment> [OPTIONS]");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $configLoader = new ConfigLoader();

        if (!isset($configLoader->getConfig()['auth']['token'])) {
            $output->writeln('You must login.');

            return;
        }

        $client->setAuthorization($configLoader->getConfig()['auth']['token']);

        try {
            $userId = (int) ($input->getOption('id') ? $input->getOption('id') : $configLoader->getConfig()['auth']['uid']);

            $response = $client->getUserProfile($userId);

            if ($response->hasError()) {
                $output->writeln(sprintf(
                    '<error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            $data = $response->getData();

            if (isset($data['username'])) {
                $output->writeln(sprintf('Username  : <comment>%s</comment>', $data['username']));
            }

            if (isset($data['gender'])) {
                $output->writeln(sprintf('Gender    : <comment>%s</comment>', $data['gender']));
            }

            if (isset($data['age'])) {
                $output->writeln(sprintf('Age       : <comment>%s</comment>', $data['age']));
            }

            if (isset($data['downloaded'], $data['uploaded'])) {
                $output->writeln('');

                $ratio = $data['uploaded'] / $data['downloaded'];

                $output->writeln(sprintf(
                    'DOWN <comment>%sB</comment> UP <comment>%sB</comment> RATIO %s',
                    Formater::humanSize((int) $data['downloaded']),
                    Formater::humanSize((int) $data['uploaded']),
                    sprintf(
                        $ratio > 1 ? '<info>%.2f</info>' : '<error>%.2f</error>',
                        $ratio
                    )
                ));
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
