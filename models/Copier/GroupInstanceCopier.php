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

namespace oat\taoGroups\models\Copier;

use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\tao\model\resources\Contract\PermissionCopierInterface;
use oat\tao\model\resources\Contract\ResourceTransferInterface;
use oat\tao\model\resources\ResourceTransferResult;
use oat\taoGroups\models\GroupsService;

class GroupInstanceCopier implements ResourceTransferInterface
{
    private GroupsService $groupsService;
    private Ontology $ontology;
    private ?PermissionCopierInterface $permissionCopier = null;

    public function __construct(GroupsService $groupsService, Ontology $ontology)
    {
        $this->groupsService = $groupsService;
        $this->ontology = $ontology;
    }

    public function withPermissionCopier(PermissionCopierInterface $permissionCopier): void
    {
        $this->permissionCopier = $permissionCopier;
    }

    /**
     * This method is to be used with tagged_iterator() from service providers
     * (but only the last copier from the iterable is effectively applied).
     */
    public function withPermissionCopiers(iterable $copiers): void
    {
        foreach ($copiers as $copier) {
            $this->withPermissionCopier($copier);
        }
    }

    public function transfer(ResourceTransferCommand $command): ResourceTransferResult
    {
        $instance = $this->ontology->getResource($command->getFrom());
        $destinationClass = $this->ontology->getClass($command->getTo());

        $copy = $this->groupsService->cloneInstance($instance, $destinationClass);

        if ($this->permissionCopier !== null) {
            $this->permissionCopier->copy(
                $command->keepOriginalAcl() ? $instance : $destinationClass,
                $copy
            );
        }

        return new ResourceTransferResult($copy->getUri());
    }
}
