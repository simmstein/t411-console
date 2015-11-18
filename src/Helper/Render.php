<?php

namespace Helper;

use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * Class Render
 * @author Simon Vieille <simon@deblan.fr>
 */
class Render
{
    public static function torrents(array $torrents, OutputInterface $output, array $options = [])
    {
        if (empty($torrents)) {
            return;
        }

        if (isset($options['sort'])) {
            $sort = $options['sort'];

            if (!in_array($sort, ['seed', 'leech', 'size', 'id', 'name'])) {
                throw new InvalidArgumentException('Invalid option "sort".');
            }

            $sort = str_replace(['seed', 'leech'], ['seeders', 'leechers'], $sort);
            $sortDatas = [];

            foreach ($torrents as $torrent) {
                $sortDatas[] = $torrent[$sort];
            }

            array_multisort(
                $sortDatas,
                isset($options['asc']) ? SORT_ASC : SORT_DESC,
                $sort === 'name' ? SORT_STRING : SORT_NUMERIC,
                $torrents
            );
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
