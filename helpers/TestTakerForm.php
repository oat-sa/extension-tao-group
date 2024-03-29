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
 * Copyright (c) 2013-2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\taoGroups\helpers;

use common_ext_ExtensionException;
use common_ext_ExtensionsManager;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\taoGroups\models\GroupsService;
use tao_helpers_form_GenerisTreeForm;

/**
 * Helper to render the groups form on the user pane
 *
 * @access public
 *
 * @author Joel Bout, <joel@taotesting.com>
 *
 * @package taoGroups
 */
class TestTakerForm
{
    /**
     * Returns a form to modify the groups a user is part of
     *
     * @param core_kernel_classes_Resource $subject
     *
     * @throws common_ext_ExtensionException
     *
     * @return string
     */
    public static function renderGroupTreeForm(core_kernel_classes_Resource $subject): string
    {
        // Ensure groups constants are loaded
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoGroups');

        $memberProperty = new core_kernel_classes_Property(GroupsService::PROPERTY_MEMBERS_URI);
        $groupForm = tao_helpers_form_GenerisTreeForm::buildTree($subject, $memberProperty);
        $groupForm->setData('title', __('Add to group'));

        return $groupForm->render();
    }

    /**
     * Returns non-rendered form
     *
     * @param core_kernel_classes_Resource $subject
     *
     * @throws common_ext_ExtensionException
     *
     * @return tao_helpers_form_GenerisTreeForm
     */
    public static function returnGroupTreeFormObject(
        core_kernel_classes_Resource $subject
    ): tao_helpers_form_GenerisTreeForm {
        // Ensure groups constants are loaded
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoGroups');

        $memberProperty = new core_kernel_classes_Property(GroupsService::PROPERTY_MEMBERS_URI);
        $groupForm = tao_helpers_form_GenerisTreeForm::buildTree($subject, $memberProperty);
        $groupForm->setData('title', __('Add to group'));

        return $groupForm;
    }
}
