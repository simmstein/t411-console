<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\ConfigLoader;
use Api\Client;

class AuthLoginCommand extends Command
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
        $dialog = $this->getHelperSet()->get('dialog');
        $client = new Client();

        if ($configLoader->configExists()) {
            $continue = $dialog->ask(
                $output,
                'The configuration file already exists. Do you want to continue? (y/n, default: y) ',
                'y'
            );

            if (!in_array($continue, ['y', 'yes'])) {
                $output->writeln('Aborded.');

                return;
            }
        }

        $username = $dialog->ask($output, 'Username: ', null);
        $password = $dialog->ask($output, 'Password: ', null);

        try {
            $response = $client->getAuthorization($username, $password);

            if ($response->hasError()) {
                $output->writeln(sprintf(
                    'Login failed: <error>%s</error> <comment>(%d)</comment>',
                    $response->getErrorMessage(),
                    $response->getErrorCode()
                ));

                return;
            }

            $configLoader->save(array(
                'auth' => array(
                    'uid' => $response->getData()['uid'],
                    'token' => $response->getData()['token'],
                )
            ));
        } catch (ClientException $e) {
            $output->writeln(sprintf('An error occured. <error>%s</error>', $e->getMessage()));
        }
    }
}
