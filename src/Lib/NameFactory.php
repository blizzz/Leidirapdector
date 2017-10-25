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

namespace Leidirapdector\Lib;


class NameFactory {
    protected const NAMESFILE = __DIR__ . '/../../res/names.dat';

    /** @var array */
    protected $names;
    protected $upperBoundaries;

    public function getGivenName(): string {
        if($this->names === null) {
            $this->loadNames();
        }
        return $this->names['fns'][rand(0, $this->upperBoundaries['firstNames'])];
    }

    public function getLastName():string {
        if($this->names === null) {
            $this->loadNames();
        }
        return $this->names['sns'][rand(0, $this->upperBoundaries['lastNames'])];
    }

    protected function loadNames() {
        $this->names = unserialize(file_get_contents(self::NAMESFILE));
        $this->upperBoundaries['firstNames'] = count($this->names['fns']) - 1;
        $this->upperBoundaries['lastNames'] = count($this->names['sns']) - 1;
    }
}
