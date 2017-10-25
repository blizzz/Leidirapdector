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

namespace Leidirapdector\Lib\Users;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MailAttribute implements IAttributePlugin {

    public function initCommand(Command $command): void
    {
        $command
            ->addOption(
            'mailAttribute',
            null,
            InputOption::VALUE_OPTIONAL,
            'Whether the mail attribute plugin should be used',
            'off'
            )
            ->addOption(
            'mailApplyRate',
            null,
            InputOption::VALUE_OPTIONAL,
            'Approx how many entries should get the attirbute in %',
            '80'
            )
            ->addOption(
            'mailDomain',
            null,
            InputOption::VALUE_OPTIONAL,
            'the domain of the email addresses to be generated',
            'example.org'
            )
            ->addOption(
            'mailRecipient',
            null,
            InputOption::VALUE_OPTIONAL,
            'hard code a recipient (all emails will be the same, otherwise uid is used)'
            );
    }

    public function get(InputInterface $input, string $uid, string $firstName, string $lastName): array
    {
        $entry = [];
        if($input->getOption('mailAttribute') === 'off') {
            return $entry;
        }
        if(rand(0, 100) > $input->getOption('mailApplyRate')) {
            return $entry;
        }
        $email = $input->getOption('mailRecipient') ?: $uid;
        $email .= '@' . $input->getOption('mailDomain');
        $entry['mail'][] = $email;
        return $entry;
    }
}
