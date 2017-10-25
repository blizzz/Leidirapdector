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


class INetOrgPersonFactory implements IUserObjectFactory {
    public function get(string $uid, string $firstName, string $lastName): array {
        $entry = [];
        $entry['objectclass'][] = 'inetOrgPerson';
        $entry['cn'] = $firstName.' '.$lastName;
        $entry['sn'] = $lastName;
        $entry['givenName'] = $firstName;
        $entry['displayName'] = $firstName.', '.$lastName;
        $entry['userPassword'] = $uid;

        return $entry;
    }
}