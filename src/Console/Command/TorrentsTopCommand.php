<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Api\ClientResponse;
use Api\ClientException;

class TorrentsTopCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:top')
            ->setDescription('Top torrents')
            ->addOption('period', 'p', InputOption::VALUE_REQUIRED, 'Period')
            ->setHelp("<info>%command.name%</info> 

Show top torrents.

Usage: <comment>torrents:search:top</comment> [OPTIONS]

<info>Period values: \"100\" (default), \"day\", \"week\", \"month\"</info>");
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
            $period = $input->getOption('period');

            if (!in_array($period, ['100', 'today', 'week', 'month'])) {
                $period = '100';
            }

            $response = $client->getTopTorrents($period);

            return $this->showResults($response, $output);
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }

    protected function showResults(ClientResponse $response, OutputInterface $output)
    {
        if ($response->hasError()) {
            $output->writeln(sprintf(
                '<error>%s</error> <comment>(%d)</comment>',
                $response->getErrorMessage(),
                $response->getErrorCode()
            ));

            return;
        }

        $output->writeln(' SEED LEECH         ID NAME');

        foreach ($response->getData() as $torrent) {
            $output->writeln(sprintf(
                '[<info>%4d</info><comment>%6d</comment>] %9d %s',
                $torrent['seeders'],
                $torrent['leechers'],
                $torrent['id'],
                $torrent['name']
            ));
        }
    }
}
