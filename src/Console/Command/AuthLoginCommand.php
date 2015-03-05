<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Api\ConfigLoader;
use Api\Client;
use Api\ClientException;

class AuthLoginCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('auth:login')
            ->setDescription('Login on t411')
            ->setHelp("<info>%command.name%</info>

Generate the config to access the API. You must have a valid login/password.

<comment>The login and the password are not saved.</comment>

Usage: <comment>auth:login</comment>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configLoader = new ConfigLoader();
        $dialog = $this->getHelperSet()->get('dialog');
        $client = new Client();

        if ($configLoader->configExists()) {
            $continue = $dialog->askConfirmation(
                $output,
                '<info>The configuration file already exists</info>. Do you want to continue? [yes] ',
                true
            );

            if (!$continue) {
                $output->writeln('Aborded.');

                return;
            }
        }

        $username = $dialog->ask($output, 'Username: ', null);
        $password = $dialog->askHiddenResponse($output, 'Password (hidden): ', null);

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
