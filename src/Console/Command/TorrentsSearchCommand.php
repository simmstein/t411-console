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
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Page number')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of results per page')
            ->addOption('sub-category', 's', InputOption::VALUE_REQUIRED, 'Filter by sub-category ID')
            ->addOption('category', 'c', InputOption::VALUE_REQUIRED, 'Filter by category ID')
            ->addOption('terms', 't', InputOption::VALUE_REQUIRED, 'Filter by terms IDs (separated by ",")')
            ->setHelp("<info>%command.name%</info>

Search torrents.

Usage: <comment>torrents:search</comment> <info>QUERY</info> [OPTIONS]

<error>--terms does not work (API bug)</error>");
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
            $categoryId = (int) $input->getOption('category');
            $termsTree = $client->getTermsTree();

            /**
             * API HACK
             * Category filter does not work
             */
            if (!empty($categoryId)) {
                $categoriesResponse = $client->getCategoriesTree();

                if ($categoriesResponse->hasError()) {
                    $output->writeln(sprintf(
                        '<error>%s</error> <comment>(%d)</comment>',
                        $response->getErrorMessage(),
                        $response->getErrorCode()
                    ));

                    return;
                }

                foreach ($categoriesResponse->getData() as $category) {
                    if (isset($category['id']) && (int) $category['id'] === $categoryId) {
                        foreach (array_keys($category['cats']) as $cid) {
                            $response = $this->searchTorrents($client, $input, $termsTree, $cid);

                            $this->showResults($response, $output);
                            $output->writeln('');
                        }
                    }
                }

                return;
            }

            $response = $this->searchTorrents($client, $input, $termsTree);

            $this->showResults($response, $output);
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }

    protected function searchTorrents(Client $client, InputInterface $input, ClientResponse $termsTree, $cid = null)
    {
        return $client->searchTorrents(
            $input->getArgument('query'),
            array(
                'offset' => (int) $input->getOption('offset'),
                'limit' => (int) $input->getOption('limit'),
                'cid' => $cid !== null ? $cid : (int) $input->getOption('sub-category'),
                'terms' => $this->convertTerms($input->getOption('terms'), $termsTree->getData()),
            )
        );
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
                            $finalTerms[$termTypeId] = [];
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

        $torrents = $response->getData()['torrents'];

        if (empty($torrents)) {
            return;
        }

        $output->writeln(' SEED LEECH         ID NAME');

        foreach ($torrents as $torrent) {
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
