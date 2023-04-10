<?php // @codingStandardsIgnoreStart

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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2023 (update and modification) Open Assessment Technologies SA
 */

// @codingStandardsIgnoreEnd

namespace oat\taoGroups\models;

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
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoGroups
 */
class GroupsService extends OntologyClassService
{
    public const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group';

    public const PROPERTY_MEMBERS_URI = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#member';

    private ?TestTakerService $testTakerService = null;

    /**
     * Return the group top level class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return $this->getClass(self::CLASS_URI);
    }

    /**
     * Delete a group instance
     *
     * @access public
     * @param core_kernel_classes_Resource $group
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function deleteGroup(core_kernel_classes_Resource $group)
    {
        return $group !== null && $group->delete(true);
    }

    /**
     * Check if the Class in parameter is a subclass of the Group Class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Class $clazz
     * @return boolean
     */
    public function isGroupClass(core_kernel_classes_Class $clazz)
    {
        return $clazz->equals($this->getRootClass())
            || $clazz->isSubClassOf($this->getRootClass());
    }

    /**
     * Get the groups of a user
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  User $user
     * @return array resources of group
     */
    public function getGroups(User $user)
    {
        return array_map(
            function (string $group): core_kernel_classes_Resource {
                return $this->getModel()->getResource($group);
            },
            $user->getPropertyValues(self::PROPERTY_MEMBERS_URI)
        );
    }

    /**
     * Gets the users of a group
     *
     * @param string $groupUri
     * @return core_kernel_classes_Resource[] resources of users
     */
    public function getUsers(string $groupUri): array
    {
        return $this->getTestTakerRootClass()->searchInstances(
            [self::PROPERTY_MEMBERS_URI => $groupUri],
            ['recursive' => true, 'like' => false]
        );
    }

    /**
     * Add a User to a Group
     *
     * @param string $userUri
     * @param core_kernel_classes_Resource $group
     * @return boolean
     */
    public function addUser($userUri, core_kernel_classes_Resource $group)
    {
        return $this->getModel()->getResource($userUri)->setPropertyValue(
            $this->getModel()->getProperty(self::PROPERTY_MEMBERS_URI),
            $group
        );
    }

    /**
     * Remove a User from a Group
     *
     * @param string $userUri
     * @param core_kernel_classes_Resource $group
     * @return boolean
     */
    public function removeUser($userUri, core_kernel_classes_Resource $group)
    {
        return $this->getModel()->getResource($userUri)->removePropertyValue(
            $this->getModel()->getProperty(self::PROPERTY_MEMBERS_URI),
            $group
        );
    }

    /**
     * Duplicates a Group, copying associations for former  Test Takers and
     * Deliveries to the new group.
     *
     * Test takers assigned to the group are not copied by the parent class
     * method (but deliveries assigned to the group are), so we need to assign
     * them here to the new group.
     *
     * @param core_kernel_classes_Resource $instance Group being cloned
     * @param ?core_kernel_classes_Class $class Class to create the duplicate in
     *
     * @throws common_Exception
     * @throws common_exception_Error
     * @throws FileExistsException
     *
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $class = null
    ) {
        $newGroup = parent::cloneInstance($instance, $class);

        foreach ($this->getUsers($instance->getUri()) as $user) {
            $this->addUser($user->getUri(), $newGroup);
        }

        return $newGroup;
    }

    private function getTestTakerRootClass(): core_kernel_classes_Class
    {
        return $this->getTestTakerService()->getRootClass();
    }

    private function getTestTakerService(): TestTakerService
    {
        return $this->testTakerService ?? TestTakerService::singleton();
    }

    public function setTestTakerService(TestTakerService $service): void
    {
        $this->testTakerService = $service;
    }
}
