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
use Leidirapdector\Lib\NameFactory;
use Leidirapdector\Lib\Users\IAttributePlugin;
use Leidirapdector\Lib\Users\INetOrgPersonFactory;
use Leidirapdector\Lib\Users\IUserObjectFactory;
use Leidirapdector\Lib\Users\JpegPhotoAttribute;
use Leidirapdector\Lib\Users\MailAttribute;
use Leidirapdector\Lib\Users\NextcloudUserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreate extends Command {
    /** @var IAttributePlugin[] */
    protected $attributePlugins;

    public function __construct($name = null) {
        $this->attributePlugins = [
            new MailAttribute(),
            new JpegPhotoAttribute()
        ];

        parent::__construct($name);
    }

    protected function configure() {
        $this->setName('create:users')
            ->setDescription('Adds users to the LDAP directory')
            ->addOption(
                'base',
                '-D',
                InputOption::VALUE_OPTIONAL,
                'Where to put new users'
            )
            ->addOption(
                'idPrefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'Prefix on the uid before applying a sequential number, e.g. "user_"',
                'user-'
            )
            ->addOption(
                'amount',
                'c',
                InputOption::VALUE_OPTIONAL,
                'How many users to create',
                1800
            )
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'start of numbering the users',
                0
            )
            ->addOption(
                'factories',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'which LDAP object generators to use',
                [INetOrgPersonFactory::class, NextcloudUserFactory::class]
            );
        foreach ($this->attributePlugins as $attributePlugin) {
            $attributePlugin->initCommand($this);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $config = new Config();
        if($config->load() === 2) {
            $output->writeln('<info>created config file with default values at '
                . realpath($config::CFGFILE) . '</info>');
        }

        if($input->getOption('base') !== null) {
            $config->setBase($input->getOption('base'));
        }

        $connection = new Connection($config);
        $connection->connect();
        $this->ensureBase($config->getBase(), $connection, $output);

        $nameFactory = new NameFactory();

        $allowedFailures = 50;
        $amount = $input->getOption('amount');

        for($i=0; $i<$amount; $i++) {
            $uid = $input->getOption('idPrefix') . ($input->getOption('offset') + $i);
            $newDN = 'uid=' . $uid . ',' . $config->getBase();
            $fn = $nameFactory->getGivenName();
            $sn = $nameFactory->getLastName();

            $entry = [];
            foreach ($input->getOption('factories') as $factoryClass) {
                /** @var IUserObjectFactory $factory */
                $factory = new $factoryClass();
                $entry = array_merge_recursive($entry, $factory->get($uid, $fn, $sn));
            }
            foreach ($this->attributePlugins as $attributePlugin) {
                $entry = array_merge_recursive($entry, $attributePlugin->get($input, $uid, $fn, $sn));
            }
            $ok = ldap_add($connection->getResource(), $newDN, $entry);
            if (!$ok) {
                if(ldap_errno($connection->getResource()) === -1) {
                    throw new RuntimeException('LDAP server went away');
                }
                $allowedFailures--;
                $amount++;
                $output->writeln('<error>Failed to create user ' . $newDN . PHP_EOL . print_r($entry, true) . '</error>');
                if ($allowedFailures === 0) {
                    throw new RuntimeException('Failed too often to create entries');
                }
            } else {
                $allowedFailures = 50;
                $output->writeln('Created user ' . $uid . ' (' . $fn . ' ' . $sn . ')');
            }
        }
    }

    protected function ensureBase(string $base, Connection $connection, OutputInterface $output) {
        $r = @ldap_read($connection->getResource(), $base, 'objectclass=*', []);
        if($r !== false) {
            //  exists
            return true;
        }
        $parts = ldap_explode_dn($base,0);
        unset($parts['count']);
        $missing = array_shift($parts);
        $this->ensureBase(implode(',', $parts), $connection, $output);

        $ouDN = $base;
        $entry['objectclass'][] = 'top';
        $entry['objectclass'][] = 'organizationalunit';
        $entry['ou'] = substr($missing, strpos($missing, '='));
        if(!ldap_add($connection->getResource(), $ouDN, $entry)) {
            throw new RuntimeException('Base does not exist and cannot be created');
        }
        $output->writeln('Created base ' . $base);
    }
}
