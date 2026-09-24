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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoGroups\test\unit\model\Copier;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\taoGroups\models\Copier\GroupInstanceCopier;
use oat\taoGroups\models\GroupsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GroupInstanceCopierTest extends TestCase
{
    private GroupsService|MockObject $groupsService;
    private Ontology|MockObject $ontology;
    private GroupInstanceCopier $sut;

    protected function setUp(): void
    {
        $this->groupsService = $this->createMock(GroupsService::class);
        $this->ontology = $this->createMock(Ontology::class);
        $this->sut = new GroupInstanceCopier($this->groupsService, $this->ontology);
    }

    public function testTransferDelegatesToGroupsServiceCloneInstance(): void
    {
        $fromUri = 'http://example.com/group#source';
        $toUri = 'http://example.com/group#destinationClass';
        $copyUri = 'http://example.com/group#copy';

        $instance = $this->createMock(core_kernel_classes_Resource::class);
        $destinationClass = $this->createMock(core_kernel_classes_Class::class);
        $copy = $this->createMock(core_kernel_classes_Resource::class);

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with($fromUri)
            ->willReturn($instance);

        $this->ontology
            ->expects($this->once())
            ->method('getClass')
            ->with($toUri)
            ->willReturn($destinationClass);

        $this->groupsService
            ->expects($this->once())
            ->method('cloneInstance')
            ->with($instance, $destinationClass)
            ->willReturn($copy);

        $copy->expects($this->once())->method('getUri')->willReturn($copyUri);

        $result = $this->sut->transfer(
            new ResourceTransferCommand(
                $fromUri,
                $toUri,
                ResourceTransferCommand::ACL_USE_DESTINATION,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );

        $this->assertSame($copyUri, $result->getDestination());
    }
}
