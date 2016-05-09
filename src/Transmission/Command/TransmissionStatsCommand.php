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
use Helper\Formater;

class TransmissionStatsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('transmission:stats')
            ->setDescription('Show stats from the transmission server')
            ->setHelp("<info>%command.name%</info>

Stats of the transmission server.

Usage: <comment>%command.name%</comment>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configLoader = new ConfigLoader();

        if (!isset($configLoader->getConfig()['transmission'])) {
            $output->writeln('No configuration found.');

            return;
        }

        $config = $configLoader->getConfig()['transmission'];

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

            $stats = $transmission->getStats();

            $output->writeln(sprintf("Active torrent(s): %d", $stats['activeTorrentCount']));

            foreach (['cumulative-stats' => 'Cumulative stats', 'current-stats' => 'Current stats'] as $k => $v) {
                $output->writeln(["", $v, str_repeat('-', strlen($v))]);
                
                $output->writeln(sprintf(
                    "Downloaded: %s", 
                    Formater::humanSize($stats[$k]['downloadedBytes'])
                ));

                $output->writeln(sprintf(
                    "Uploaded: %s", 
                    Formater::humanSize($stats[$k]['uploadedBytes'])
                ));
                
                $output->writeln(sprintf(
                    "Files Added: %d", 
                    $stats[$k]['filesAdded']
                ));
                
                $output->writeln(sprintf(
                    "Seconds Active: %d", 
                    $stats[$k]['secondsActive']
                ));
                
                $output->writeln(sprintf(
                    "Sessions: %d", 
                    $stats[$k]['sessionCount']
                ));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                'An error occured. <error>%s</error>',
                $e->getMessage()
            ));
            
            return;
        }
    }
}
