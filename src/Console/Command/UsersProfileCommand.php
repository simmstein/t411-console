<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Symfony\Component\Console\Input\InputOption;

class UsersProfileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users:profile')
            ->setDescription('')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, '')
            ->setHelp("<info>%command.name%</info>
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
                    $this->getHumainSize($data['downloaded']),
                    $this->getHumainSize($data['uploaded']),
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

    protected function getHumainSize($bytes, $decimals = 2)
    {
        $sizes = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sizes[$factor];
    }
}
