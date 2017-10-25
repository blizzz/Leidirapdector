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

class JpegPhotoAttribute implements IAttributePlugin {

    public function initCommand(Command $command): void
    {
        $command
            ->addOption(
                'jpegPhotoAttribute',
                null,
                InputOption::VALUE_OPTIONAL,
                'Whether the jpegPhoto attribute plugin should be used',
                'off'
            )
            ->addOption(
                'jpegPhotoApplyRate',
                null,
                InputOption::VALUE_OPTIONAL,
                'Approx how many entries should get the attirbute in %',
                '80'
            );
    }

    public function get(InputInterface $input, string $uid, string $firstName, string $lastName): array {
        $entry = [];
        if($input->getOption('jpegPhotoAttribute') === 'off') {
            return $entry;
        }
        if(rand(0, 100) > $input->getOption('jpegPhotoApplyRate')) {
            return $entry;
        }
        $avatarFile = __DIR__ . '/../../../res/avatars/avatar' . rand(1, 199) . '.png';
        $avatar = file_get_contents($avatarFile);
        $entry['jpegPhoto'] = $avatar;
        return $entry;
    }
}
