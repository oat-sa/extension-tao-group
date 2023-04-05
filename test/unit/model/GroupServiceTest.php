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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoDacSimple\test;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use oat\tao\model\taskQueue\QueueDispatcher;
use oat\taoGroups\models\GroupsService;
use oat\taoTestTaker\models\TestTakerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GroupServiceTest extends TestCase
{
    /** @var GroupsService */
    private $sut;

    /** @var User|MockObject */
    private $userMock;

    public function setUp(): void
    {
        $this->sut = new GroupsService();
        $this->userMock = $this->createMock(User::class);
        $this->testTakerServiceMock = $this->createMock(TestTakerService::class);

        $this->sut->setTestTakerService($this->testTakerServiceMock);
    }

    public function testGetGroups(): void
    {
        $this->userMock
            ->expects($this->once())
            ->method('getPropertyValues')
            ->with(GroupsService::PROPERTY_MEMBERS_URI)
            ->willReturn(
                [
                    'http://example.com/group1',
                    'http://example.com/group2'
                ]
            );

        $groups = $this->sut->getGroups($this->userMock);

        $this->assertContainsOnlyInstancesOf(
            core_kernel_classes_Resource::class,
            $groups
        );
        $this->assertEquals('http://example.com/group1', $groups[0]->getUri());
        $this->assertEquals('http://example.com/group2', $groups[1]->getUri());
    }

    public function testGetUsers(): void
    {
        $ttRootClassMock = $this->createMock(core_kernel_classes_Class::class);
        $ttRootClassMock
            ->expects($this->once())
            ->method('searchInstances')
            ->with(
                [GroupsService::PROPERTY_MEMBERS_URI => 'http://example.com/group1'],
                ['recursive' => true, 'like' => false]
            )
            ->willReturn([$this->userMock]);

        $this->testTakerServiceMock
            ->expects($this->once())
            ->method('getRootClass')
            ->willReturn($ttRootClassMock);

        $users = $this->sut->getUsers('http://example.com/group1');

        $this->assertCount(1, $users);
        $this->assertEquals($this->userMock, $users[0]);
    }

    /**
     * @depends testGetUsers
     */
    public function testCloneInstance(): void
    {
        $classMock = $this->createMock(core_kernel_classes_Class::class);
        $groupMock = $this->createMock(core_kernel_classes_Resource::class);
        $newGroupMock = $this->createMock(core_kernel_classes_Resource::class);

        $classMock
            ->expects($this->once())
            ->method('createInstance')
            ->with($this->anything())
            ->willReturn($newGroupMock);

        $classMock
            ->method('getLabel')
            ->willReturn('Class Label');

        $classMock
            ->method('getInstances')
            ->willReturn([]);

        $classMock
            ->method('getProperties')
            ->with(true)
            ->willReturn([]);

        $newGroupMock
            ->expects($this->once())
            ->method('setLabel')
            ->with($this->anything());

        $ttRootClassMock = $this->createMock(core_kernel_classes_Class::class);
        $ttRootClassMock
            ->expects($this->once())
            ->method('searchInstances')
            ->with(
                [GroupsService::PROPERTY_MEMBERS_URI => 'http://example.com/group1'],
                ['recursive' => true, 'like' => false]
            )
            ->willReturn([$this->userMock]);

        $this->testTakerServiceMock
            ->expects($this->once())
            ->method('getRootClass')
            ->willReturn($ttRootClassMock);


        // @todo Mock the getDataLanguage() call from GenerisServiceTrait
        //       (called by GenerisServiceTrait::cloneInstance()).
        /*
         * 1) oat\taoDacSimple\test\GroupServiceTest::testCloneInstance
         *
         *      Error: Call to a member function get() on null
         *
         *      tao/models/classes/GenerisServiceTrait.php:109
         *      tao/models/classes/GenerisServiceTrait.php:83
         *      tao/models/classes/GenerisServiceTrait.php:182
         *      taoGroups/models/GroupsService.php:168
         *      taoGroups/test/unit/model/GroupServiceTest.php:151
         */
        $result = $this->sut->cloneInstance($groupMock, $classMock);
        $this->assertSame($newGroupMock, $result);

        // @todo Test if the users have been copied
        //       (i.e. if addUser / $user->setPropertyValue() has been called
        //        for former users in the copied group)


        $this->markTestIncomplete('Work in Progress');
    }
}
