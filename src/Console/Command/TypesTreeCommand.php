<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Symfony\Component\Console\Input\InputOption;
use Api\ClientException;

class TypesTreeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('types:tree')
            ->setDescription('Show types and terms')
            ->addOption('terms', 't', InputOption::VALUE_NONE, 'Show terms')
            ->addOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter types by ID or by name')
            ->setHelp("<info>%command.name%</info>
			
List all types of terms and terms.

Usage: <comment>types:tree</comment> [OPTIONS]");
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
            $categoriesReponse = $client->getCategoriesTree();
            $termsResponse = $client->getTermsTree();

            foreach (array($categoriesReponse, $termsResponse) as $response) {
                if ($response->hasError()) {
                    $output->writeln(sprintf(
                        '<error>%s</error> <comment>(%d)</comment>',
                        $response->getErrorMessage(),
                        $response->getErrorCode()
                    ));

                    return;
                }
            }

            $filter = $input->getOption('filter');

            if (!empty($filter)) {
                if (is_numeric($filter)) {
                    $filter = (int) $filter;
                }
            }

            foreach ($termsResponse->getData() as $categoryId => $termTypes) {
                $stop = false;

                foreach ($termTypes as $termTypeId => $termType) {
                    if ($filter !== null) {
                        if (is_int($filter)) {
                            if ((int) $termTypeId === $filter) {
                                $stop = true;
                            } else {
                                continue;
                            }
                        } else {
                            if (0 === preg_match(sprintf('/%s/U', preg_quote($filter)), $termType['type'])) {
                                continue;
                            }
                        }
                    }

                    $output->writeln(sprintf('<comment>%3d</comment> <info>%s</info>', $termTypeId, $termType['type']));

                    if ($input->getOption('terms')) {
                        $isFirst = true;
                        foreach ($termType['terms'] as $termId => $term) {
                            $char = '|';
                            if ($isFirst) {
                                $isFirst = false;
                                $char = '`';
                            }

                            $output->writeln(sprintf('   %s- <comment>%4d</comment> %s', $char, $termId, $term));
                        }

						$output->writeln('');
                    }

                    if ($stop) {
                        return;
                    }
                }
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
