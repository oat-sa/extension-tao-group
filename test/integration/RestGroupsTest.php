<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoGroups\test\integration;

use oat\tao\test\integration\RestTestCase;
use oat\taoGroups\models\GroupsService;

// phpcs:disable
include_once dirname(__FILE__) . '/../../includes/raw_start.php';
// phpcs:enable

/**
 * @author Lionel Lecaque
 */
class RestGroupsTest extends RestTestCase
{
    public function serviceProvider()
    {
        return [
            ['taoGroups/Api',GroupsService::CLASS_URI],
        ];
    }
}
