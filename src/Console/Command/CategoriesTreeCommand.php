<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;

class CategoriesTreeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('categories:tree')
            ->setDescription('Show categories')
            ->setHelp("The <info>%command.name%</info> show the categories");
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
            $response = $client->getCategoriesTree();

            if ($response->hasError()) {
                $output->writeln(sprintf(
                    '<error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            foreach ($response->getData() as $category) {
                if (!isset($category['name'])) {
                    $category['name'] = 'Category name not defined';
                }

                $output->writeln(sprintf('<comment>%3d</comment> <info>%s</info>', isset($category['id']) ? $category['id'] : null, $category['name']));

                if (!empty($category['cats'])) {
                    $isFirst = true;

                    foreach ($category['cats'] as $subCategoryId => $subCategory) {
                        $char = '|';
                        if ($isFirst) {
                            $isFirst = false;
                            $char = '`';
                        }

                        $output->writeln(sprintf('   %s- <comment>%4d</comment> %s', $char, $subCategoryId, $subCategory['name']));
                    }
                }

                $output->writeln('');
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
