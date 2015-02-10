<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;

class TorrentsSearchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:search')
            ->setDescription('Search torrents')
            ->addArgument('query', InputArgument::REQUIRED, 'Query')
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'Search offset')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Search limit')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'Category')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Type')
            ->addOption('term', null, InputOption::VALUE_OPTIONAL, 'Term')
            ->setHelp("The <info>%command.name%</info> search torrents");
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
            $response = $client->searchTorrents(
                $input->getArgument('query'),
                array(
                    'offset' => (int) $input->getOption('offset'),
                    'limit' => (int) $input->getOption('limit'),
                    'category' => $input->getOption('category'),
                    'type' => $input->getOption('type'),
                    'term' => $input->getOption('term'),
                )
            );

            if ($response->hasError()) {
                $output->writeln(sprintf(
                    '<error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            $output->writeln('                  ID NAME');

            foreach ($response->getData()['torrents'] as $torrent) {
                $output->writeln(sprintf(
                    '[<info>%4d</info><comment>%4d</comment>] %9d %s',
                    $torrent['seeders'],
                    $torrent['leechers'],
                    $torrent['id'],
                    $torrent['name']
                ));
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
