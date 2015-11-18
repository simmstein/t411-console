<?php

namespace Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Api\ClientResponse;

/**
 * Class Render
 * @author Simon Vieille <simon@deblan.fr>
 */
class Render
{
    public static function torrents(array $torrents, OutputInterface $output)
    {
        if (empty($torrents)) {
            return;
        }

        $output->writeln(' SEED LEECH   SIZE      ID      NAME');

        foreach ($torrents as $torrent) {
            $output->writeln(sprintf(
                '[<info>%4d</info><comment>%6d</comment>] [%8s] %7d %s',
                $torrent['seeders'],
                $torrent['leechers'],
                Formater::humanSize((int) $torrent['size']),
                $torrent['id'],
                $torrent['name']
            ));
        }
    }
}

