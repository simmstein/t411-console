<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

class TorrentsSearchMoviesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('torrents:search:movies')
            ->setDescription('Search movies')
            ->addArgument('query', InputArgument::REQUIRED, 'Query')
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Page number')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of results per page')
            ->addOption('terms', 't', InputOption::VALUE_REQUIRED, 'Filter by terms IDs (separated by ",")')
            ->addOption('sort', null, InputOption::VALUE_REQUIRED, 'Sort')
            ->addOption('asc', null, InputOption::VALUE_NONE, 'Ascending sort')
            ->setHelp("<info>%command.name%</info> 
			
Search movies.

Usage: <comment>torrents:search:movies</comment> <info>QUERY</info> [OPTIONS]

<error>--terms does not work (API bug)</error>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputData = array(
            'command' => 'torrents:search',
            'query' => $input->getArgument('query'),
            '--sub-category' => 631,
        );

        foreach (['offset', 'limit', 'terms', 'sort', 'asc'] as $p) {
            $value = $input->getOption($p);

            if (null !== $value) {
                $inputData['--'.$p] = $value;
            }
        }

        return $this->getApplication()->doRun(new ArrayInput($inputData), $output);
    }
}
