<?php
/**
 * @copyright Copyright (c) 2017 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Leidirapdector\Commands;


use Leidirapdector\Config\Config;
use Leidirapdector\Lib\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends Command {
    protected function configure() {
        $this->setName('delete:entry')
            ->setDescription('Delete a single entry')
            ->addArgument('identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $config = new Config();
        if($config->load() === 2) {
            $output->writeln('<info>created config file with default values at '
                . realpath($config::CFGFILE) . '</info>');
        }

        $connection = new Connection($config);
        $connection->connect();

        $dn = $input->getArgument('identifier');
        if(ldap_explode_dn($dn, 0) === false) {
            throw new \InvalidArgumentException('Not a valid DN');
        }

        if(@ldap_delete($connection->getResource(), $dn)) {
            $output->writeln('Entry deleted');
        } else {
            $e = ldap_error($connection->getResource());
            $output->writeln('<error>Could not delete entry, because:</error>');
            $output->writeln("\t" . $e);
        }
    }
}
