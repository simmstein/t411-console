<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;

class TorrentsDetailsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:details')
            ->setDescription('Show a torrent details')
            ->addArgument('id', InputArgument::REQUIRED, 'Torrent ID')
            ->setHelp("<info>%command.name%</info>

Show torrent details.

Usage: <comment>torrents:details</comment> <info>TORRENT_ID</info>");
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
            $response = $client->getTorrentDetails($input->getArgument('id'));

            if ($response->hasError()) {
                $output->writeln(sprintf(
                    '<error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            $data = $response->getData();

            $output->writeln(sprintf('<info>%s</info>', $data['name']));
            $output->writeln('');
            $output->writeln(sprintf('Category        : <comment>%s</comment>', $data['categoryname']));

            foreach ($data['terms'] as $title => $value) {
                $output->writeln(sprintf('%-16s: <comment>%s</comment>', $title, $value));
            }

            $output->writeln('');
            $output->writeln($this->parseDescription($data['description']));
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }

    protected function parseDescription($description)
    {
        $description = str_replace('<br>', PHP_EOL, $description);
        $description = trim(html_entity_decode(strip_tags($description)));

        return $description;
    }
}
