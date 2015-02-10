<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;

class TermsTreeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('terms:tree')
            ->setDescription('Show terms')
            ->setHelp("The <info>%command.name%</info> show the terms");
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

            foreach ($termsResponse->getData() as $categoryId => $termTypes) {
                foreach ($categoriesReponse->getData() as $id => $category) {
                    if ($categoryId === $id) {
                        if (isset($category['name'])) {
                            $output->writeln(sprintf('`- %s', $category['name']));
                        } else {
                            $output->writeln(sprintf('`- %s', 'Not defined'));
                        }
                    }

                    foreach ($termTypes as $termType) {
                        if (isset($termType['type'])) {
                            $output->writeln(sprintf('   +- %s', $termType['type']));
                        }

                        foreach ($termType['terms'] as $term) {
                            $output->writeln(sprintf('   |   %s', $term));
                        }
                    }
                    $output->writeln('');
                }
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
