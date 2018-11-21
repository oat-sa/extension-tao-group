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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               
namespace oat\taoGroups\helpers;

use oat\taoGroups\models\GroupAssignment;
/**
 * Helper to render the delivery form on the group page
 * 
 * @author joel bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class GroupWidget
{
	public static function renderDeliveryTree(\core_kernel_classes_Resource $group) {

		$property = new \core_kernel_classes_Property(GroupAssignment::PROPERTY_GROUP_DELIVERY);
		$tree = \tao_helpers_form_GenerisTreeForm::buildTree($group, $property);
		$tree->setData('title', __('Deliveries'));
		return $tree->render();

	}
}