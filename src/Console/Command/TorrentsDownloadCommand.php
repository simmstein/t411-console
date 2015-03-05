<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use GuzzleHttp\Exception\ParseException;
use Symfony\Component\Filesystem\Filesystem;
use Api\ClientException;

class TorrentsDownloadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:download')
            ->setDescription('Download a torrent')
            ->addArgument('id', InputArgument::REQUIRED, 'Torrent ID')
            ->addArgument('output_file', InputArgument::REQUIRED, 'Output')
            ->setHelp("<info>%command.name%</info> 

Download a torrent.

Usage: <comment>torrents:download</comment> <info>TORRENT_ID OUTPUT</info>

<info>OUTPUT</info> could be a file or STDIN by using <info>-</info>.");
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
            $response = $client->downloadTorrent($input->getArgument('id'));

            try {
                if ($response->hasError()) {
                    $output->writeln(sprintf(
                        '<error>%s</error> <comment>(%d)</comment>',
                        $response->getErrorMessage(),
                        $response->getErrorCode()
                    ));

                    return;
                }
            } catch (ParseException $e) {

            }

            $outputFile = $input->getArgument('output_file');

            if ($outputFile === '-') {
                echo $response->getBody();
            } else {
                $filesystem = new Filesystem();
                $filesystem->dumpFile($outputFile, $response->getBody());
                $output->writeln(sprintf('Torrent saved in %s', $outputFile));
            }
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
