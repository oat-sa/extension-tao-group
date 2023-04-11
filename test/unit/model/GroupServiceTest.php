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

namespace oat\taoGroups\test\unit\model;

use oat\generis\model\data\Ontology;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\ServiceManagerMockTrait;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use oat\taoGroups\models\GroupsService;
use oat\taoTestTaker\models\TestTakerService;
use common_session_Session;
use core_kernel_classes_Class;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GroupServiceTest extends TestCase
{
    use ServiceManagerMockTrait;

    /** @var GroupsService */
    private $sut;

    /** @var User|MockObject */
    private $userMock;

    /** @var Ontology|MockObject */
    private $ontology;

    /** @var core_kernel_classes_Resource|MockObject */
    private $group1Mock;

    /** @var core_kernel_classes_Resource|MockObject */
    private $group2Mock;

    public function setUp(): void
    {
        $this->userMock = $this->createMock(User::class);
        $this->testTakerServiceMock = $this->createMock(TestTakerService::class);
        $this->group1Mock = $this->createMock(core_kernel_classes_Resource::class);
        $this->group2Mock = $this->createMock(core_kernel_classes_Resource::class);
        $this->ontology = $this->createMock(Ontology::class);

        $this->group1Mock->method('getUri')->willReturn('http://example.com/group1');
        $this->group2Mock->method('getUri')->willReturn('http://example.com/group2');

        $this->sut = new GroupsService();
        $this->sut->setTestTakerService($this->testTakerServiceMock);
        $this->sut->setModel($this->ontology);
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

        $this->ontology
            ->expects($this->exactly(2))
            ->method('getResource')
            ->withConsecutive(
                ['http://example.com/group1'],
                ['http://example.com/group2']
            )
            ->willReturnOnConsecutiveCalls(
                $this->group1Mock,
                $this->group2Mock
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
        $membersProperty = $this->createMock(core_kernel_classes_Property::class);
        $this->ontology
            ->expects($this->once())
            ->method('getProperty')
            ->with(GroupsService::PROPERTY_MEMBERS_URI)
            ->willReturn($membersProperty);

        $newGroupMock = $this->createMock(core_kernel_classes_Resource::class);
        $newGroupMock
            ->method('setLabel')  // Called by parent::cloneInstance()
            ->with($this->stringStartsWith('Former group'));

        $this->userMock = $this->createMock(core_kernel_classes_Resource::class);
        $this->userMock
            ->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com/user1');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('http://example.com/user1')
            ->willReturn($this->userMock);

        $classMock = $this->createMock(core_kernel_classes_Class::class);
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
        $classMock
            ->expects($this->once())
            ->method('createInstance') // Called from GenerisServiceTrait
            ->with($this->stringStartsWith('Class Label'))
            ->willReturn($newGroupMock);

        $groupMock = $this->createMock(core_kernel_classes_Resource::class);
        $groupMock
            ->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com/group1');
        $groupMock
            ->method('getLabel')
            ->willReturn('Former group');

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

        $classMock
            ->expects($this->once())
            ->method('searchInstances')
            ->with(
                [OntologyRdfs::RDFS_LABEL => 'Class Label 1'],
                $this->anything()
            )
            ->willReturn([]);

        // Mocks needed for the call to parent::cloneInstance()
        $this->sut->setServiceManager(
            $this->getServiceManagerMock([
                SessionService::SERVICE_ID => $this->getSessionServiceMock(),
            ])
        );

        // Tests if a call is made to assign the former users to the new group
        $this->userMock
            ->expects($this->once())
            ->method('setPropertyValue')
            ->with($membersProperty, $newGroupMock)
            ->willReturn(true);

        $result = $this->sut->cloneInstance($groupMock, $classMock);
        $this->assertSame($newGroupMock, $result);
    }

    /**
     * SessionService needs to be mocked because it is used by cloneInstance()
     * (inherited from GenerisServiceTrait).
     */
    private function getSessionServiceMock(): SessionService
    {
        $sessionMock = $this->createMock(common_session_Session::class);
        $sessionMock
            ->method('getDataLanguage')
            ->willReturn('en-US');

        $sessionServiceMock = $this->createMock(SessionService::class);
        $sessionServiceMock
            ->method('getCurrentSession')
            ->willReturn($sessionMock);

        return $sessionServiceMock;
    }
}
