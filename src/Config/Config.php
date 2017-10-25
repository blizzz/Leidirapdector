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

namespace Leidirapdector\Config;


class Config {
    public const CFGFILE = __DIR__ . '/../../cfg/config.json';

    protected $config = [
        'server'  => 'ldap://127.0.0.1:389',
        'adminDN' => '',
        'adminPwd' => '',
        'base' => 'dc=localhost',
    ];

    public function getServer(): string {
        return $this->config['server'];
    }

    public function getAdminDN(): string {
        return $this->config['adminDN'];
    }

    public function getAdminPwd(): string {
        return $this->config['adminPwd'];
    }

    public function getBase(): string {
        return $this->config['base'];
    }

    public function setBase(string $base): void {
        $this->config['base'] = $base;
    }

    public function load(): int {
        $data = @file_get_contents(self::CFGFILE);
        if(!$data) {
            file_put_contents(self::CFGFILE, json_encode($this->config, JSON_PRETTY_PRINT));
            return 2;
        }
        $custom = json_decode($data, true);
        $this->config = array_merge($this->config, $custom);
        return 1;
    }
}
