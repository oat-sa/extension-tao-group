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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2023 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoGroups\models;

use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\TestTakerService;
use oat\oatbox\user\User;
use oat\tao\model\OntologyClassService;
use common_Exception;
use common_exception_Error;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use League\Flysystem\FileExistsException;

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 * @access public
 * @package taoGroups
 */
class GroupsService extends OntologyClassService
{
    public const CLASS_URI = TaoOntology::CLASS_URI_GROUP;

    public const PROPERTY_MEMBERS_URI = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#member';

    /**
     * Returns the group top level class.
     */
    public function getRootClass(): core_kernel_classes_Class
    {
        return $this->getClass(self::CLASS_URI);
    }

    /**
     * Deletes a group instance.
     */
    public function deleteGroup(core_kernel_classes_Resource $group): bool
    {
        return $group !== null && $group->delete(true);
    }

    /**
     * Check if a given class is a subclass of the Group root class.
     */
    public function isGroupClass(core_kernel_classes_Class $clazz): bool
    {
        return $clazz->equals($this->getRootClass())
            || $clazz->isSubClassOf($this->getRootClass());
    }

    /**
     * Get the groups of a user.
     *
     * @return core_kernel_classes_Resource[] Group resources
     */
    public function getGroups(User $user): array
    {
        return array_map(
            fn (string $group): core_kernel_classes_Resource => $this->getModel()->getResource($group),
            $user->getPropertyValues(self::PROPERTY_MEMBERS_URI)
        );
    }

    /**
     * Gets the users of a group.
     *
     * @return core_kernel_classes_Resource[] User resources
     */
    public function getUsers(string $groupUri): array
    {
        return $this->getTestTakerService()->getRootClass()->searchInstances(
            [self::PROPERTY_MEMBERS_URI => $groupUri],
            ['recursive' => true, 'like' => false]
        );
    }

    /**
     * Adds a user to a Group.
     */
    public function addUser(string $userUri, core_kernel_classes_Resource $group): bool
    {
        return $this->getModel()->getResource($userUri)->setPropertyValue(
            $this->getModel()->getProperty(self::PROPERTY_MEMBERS_URI),
            $group
        );
    }

    /**
     * Removes a user from a Group.
     */
    public function removeUser(string $userUri, core_kernel_classes_Resource $group): bool
    {
        return $this->getModel()->getResource($userUri)->removePropertyValue(
            $this->getModel()->getProperty(self::PROPERTY_MEMBERS_URI),
            $group
        );
    }

    /**
     * Creates a duplicate of the given group instance into the given class,
     * copying associations for former Test Takers and Deliveries to the new group.
     *
     * @throws common_Exception
     * @throws common_exception_Error
     * @throws FileExistsException
     */
    public function cloneInstance(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $class = null
    ): core_kernel_classes_Resource {
        $newGroup = parent::cloneInstance($instance, $class);

        // Test takers assigned to the group are not copied by the parent class
        // method (but deliveries assigned to the group are), so we need to
        // assign them here to the new group.
        //
        foreach ($this->getUsers($instance->getUri()) as $user) {
            $this->addUser($user->getUri(), $newGroup);
        }

        return $newGroup;
    }

    protected function getTestTakerService(): TestTakerService
    {
        return $this->getServiceManager()->getContainer()->get(TestTakerService::class);
    }
}
