<?php

namespace Transmission\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Api\ConfigLoader;
use Symfony\Component\Console\Input\ArrayInput;
use Vohof\Transmission;
use Transmission\Client\GuzzleClient;

class TransmissionDownloadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('transmission:download')
            ->setDescription('Download a torrent')
            ->addArgument('id', InputArgument::REQUIRED, 'Torrent ID')
            ->setHelp("<info>%command.name%</info>

Download a torrent.

Usage: <comment>%command.name%</comment> <info>TORRENT_ID</info>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configLoader = new ConfigLoader();

        if (!isset($configLoader->getConfig()['transmission'])) {
            $output->writeln('No configuration found.');

            return;
        }

        $config = $configLoader->getConfig()['transmission'];

        $outputFile = sprintf(
            '%s/t411-console-%d',
            sys_get_temp_dir(),
            time()
        );

        $inputData = array(
            'command' => 'torrents:download',
            'id' => $input->getArgument('id'),
            'output_file' => $outputFile,
            '-q' => true,
        );

        $options = [];

        if (!empty($config['username']) && !empty($config['password'])) {
            $options = array(
                'request.options' => array(
                    'auth' => array($config['username'], $config['password'])
                )
            );
        }

        try {
            $client = new GuzzleClient($config['host'], $options);

            $transmission = new Transmission($configLoader->getConfig()['transmission'], $client);

            $this->getApplication()->doRun(new ArrayInput($inputData), $output);

            $content = base64_encode(file_get_contents($outputFile));

            $transmission->add($content, true);
        } catch (\Exception $e) {
            unlink($outputFile);

            $output->writeln(sprintf(
                'An error occured. <error>%s</error>',
                $e->getMessage()
            ));

            $output->writeln(sprintf('Torrent %s removed', $outputFile));

            return;
        }

        unlink($outputFile);

        $output->writeln(sprintf('Download <info>started</info>.', $outputFile));
        $output->writeln(sprintf('Torrent %s removed', $outputFile));
    }
}
