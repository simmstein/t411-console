<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Api\ClientResponse;

class TorrentsSearchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:search')
            ->setDescription('Search torrents')
            ->addArgument('query', InputArgument::REQUIRED, 'Query')
            ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Search offset')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Search limit')
            ->addOption('category', 'c', InputOption::VALUE_OPTIONAL, 'Category')
            ->addOption('terms', 't', InputOption::VALUE_OPTIONAL, 'Terms')
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
                    'cat' => (int) $input->getOption('category'),
                    'terms' => $this->convertTerms($input->getOption('terms'), $client->getTermsTree()->getData()),
                )
            );

            return $this->showResults($response, $output);
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }

    public function convertTerms($value, array $termTypesTree)
    {
        $value = trim($value);

        $terms = array_map(
            function ($v) {
                return (int) trim($v);
            },
            explode(',', $value)
        );

        $finalTerms = array();

        foreach ($termTypesTree as $termTypes) {
            foreach ($termTypes as $termTypeId => $termType) {
                foreach ($terms as $term) {
                    if (isset($termType['terms'][$term])) {
                        if (!isset($finalTerms[$termTypeId])) {
                            $finalTerms[$termTypeId] = array();
                        }

                        if (!in_array($term, $finalTerms[$termTypeId])) {
                            $finalTerms[$termTypeId][] = $term;
                        }
                    }
                }
            }
        }

        return $finalTerms;
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

        foreach ($response->getData()['torrents'] as $torrent) {
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
