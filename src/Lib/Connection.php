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


use Leidirapdector\Config\Config;

class Connection {
    /** @var  resource */
    protected $resource;

    /** @var Config */
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getResource()  {
        return $this->resource;
    }

    public function connect() {
        $this->resource = ldap_connect($this->config->getServer());
        ldap_set_option($this->resource, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($this->resource, $this->config->getAdminDN(), $this->config->getAdminPwd());
    }
}
