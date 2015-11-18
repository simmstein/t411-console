<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Api\Client;
use Api\ConfigLoader;
use Api\ClientResponse;
use Api\ClientException;
use Helper\Render;

class TorrentsTopCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:top')
            ->setDescription('Top torrents')
            ->addOption('period', 'p', InputOption::VALUE_REQUIRED, 'Period')
            ->addOption('sort', null, InputOption::VALUE_REQUIRED, 'Sort')
            ->addOption('asc', null, InputOption::VALUE_NONE, 'Ascending sort')
            ->setHelp("<info>%command.name%</info> 

Show top torrents.

Usage: <comment>torrents:search:top</comment> [OPTIONS]

<info>Period values</info> \"100\" (default), \"day\", \"week\", \"month\"
<info>Sort values</info>   \"seed\", \"leech\", \"size\", \"name\", \"id\"
");
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
            $period = $input->getOption('period');

            if (!in_array($period, ['100', 'today', 'week', 'month'])) {
                $period = '100';
            }

            $response = $client->getTopTorrents($period);
            
            if ($response->hasError()) {
                $output->writeln(sprintf(
                    '<error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            $options = [];
            
            if ($input->getOption('sort')) {
                $options['sort'] = $input->getOption('sort');
            }
            
            if ($input->getOption('asc')) {
                $options['asc'] = true;
            }

            Render::torrents($response->getData(), $output, $options);
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
