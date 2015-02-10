<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class AuthCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('auth:login')
            ->setDescription('Login on t411')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configLoader = new ConfigLoader();

        if ($configLoader->configExists()) {
            $continue = $dialog->askConfirmation(
                $output,
                $dialog->getQuestion(
                    'The configuration file already exists. Do you want to continue',
                    'yes',
                    '?'
                ),
                true
            );

            if (!$continue) {
                $output->writeln('Aborded.');

                return;
            }
        }
    }
}
