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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoGroups\test\unit\model\Copier;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\tao\model\resources\Contract\PermissionCopierInterface;
use oat\taoGroups\models\Copier\GroupInstanceCopier;
use oat\taoGroups\models\GroupsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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

    /**
     * @dataProvider aclModeAndPermissionCopierSourceProvider
     */
    public function testTransferPassesExpectedAclSourceToPermissionCopier(
        string $aclMode,
        bool $useInstanceAsAclSource
    ): void {
        $instance = $this->createMock(core_kernel_classes_Resource::class);
        $destinationClass = $this->createMock(core_kernel_classes_Class::class);
        $copy = $this->createMock(core_kernel_classes_Resource::class);
        $aclSource = $useInstanceAsAclSource ? $instance : $destinationClass;

        $this->ontology->method('getResource')->willReturn($instance);
        $this->ontology->method('getClass')->willReturn($destinationClass);
        $this->groupsService->method('cloneInstance')->willReturn($copy);
        $copy->method('getUri')->willReturn('http://example.com/group#copy');

        $permissionCopier = $this->createMock(PermissionCopierInterface::class);
        $permissionCopier
            ->expects($this->once())
            ->method('copy')
            ->with($aclSource, $copy);

        $this->sut->withPermissionCopier($permissionCopier);

        $this->sut->transfer(
            new ResourceTransferCommand(
                'http://example.com/group#source',
                'http://example.com/group#destinationClass',
                $aclMode,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );
    }

    public function aclModeAndPermissionCopierSourceProvider(): array
    {
        return [
            'keep original acl' => [ResourceTransferCommand::ACL_KEEP_ORIGINAL, true],
            'use destination acl' => [ResourceTransferCommand::ACL_USE_DESTINATION, false],
        ];
    }

    public function testTransferPropagatesCloneInstanceFailure(): void
    {
        $instance = $this->createMock(core_kernel_classes_Resource::class);
        $destinationClass = $this->createMock(core_kernel_classes_Class::class);

        $this->ontology->method('getResource')->willReturn($instance);
        $this->ontology->method('getClass')->willReturn($destinationClass);
        $this->groupsService
            ->method('cloneInstance')
            ->willThrowException(new RuntimeException('Unable to clone group'));

        $permissionCopier = $this->createMock(PermissionCopierInterface::class);
        $permissionCopier->expects($this->never())->method('copy');
        $this->sut->withPermissionCopier($permissionCopier);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to clone group');

        $this->sut->transfer(
            new ResourceTransferCommand(
                'http://example.com/group#source',
                'http://example.com/group#destinationClass',
                ResourceTransferCommand::ACL_USE_DESTINATION,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );
    }

    public function testTransferInvokesEveryPermissionCopier(): void
    {
        $instance = $this->createMock(core_kernel_classes_Resource::class);
        $destinationClass = $this->createMock(core_kernel_classes_Class::class);
        $copy = $this->createMock(core_kernel_classes_Resource::class);

        $this->ontology->method('getResource')->willReturn($instance);
        $this->ontology->method('getClass')->willReturn($destinationClass);
        $this->groupsService->method('cloneInstance')->willReturn($copy);
        $copy->method('getUri')->willReturn('http://example.com/group#copy');

        $firstCopier = $this->createMock(PermissionCopierInterface::class);
        $secondCopier = $this->createMock(PermissionCopierInterface::class);

        $firstCopier->expects($this->once())->method('copy')->with($destinationClass, $copy);
        $secondCopier->expects($this->once())->method('copy')->with($destinationClass, $copy);

        $this->sut->withPermissionCopiers([$firstCopier, $secondCopier]);

        $this->sut->transfer(
            new ResourceTransferCommand(
                'http://example.com/group#source',
                'http://example.com/group#destinationClass',
                ResourceTransferCommand::ACL_USE_DESTINATION,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );
    }
}
