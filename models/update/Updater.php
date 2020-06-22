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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoGroups\models\update;

use oat\taoGroups\models\GroupsService;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\user\TaoRoles;
use oat\taoGroups\controller\Api;

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoGroups

 * @deprecated use migrations instead. See https://github.com/oat-sa/generis/wiki/Tao-Update-Process
 */
class Updater extends \common_ext_ExtensionUpdater
{
    const OLD_MEMBER_PROPERTY = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Members';

    /**
     * (non-PHPdoc)
     * @see common_ext_ExtensionUpdater::update()
     */
    public function update($initialVersion)
    {
        if ($this->isVersion('2.6')) {
            OntologyUpdater::syncModels();

            $iterator = new \core_kernel_classes_ResourceIterator([GroupsService::singleton()->getRootClass()]);
            foreach ($iterator as $group) {
                $users = $group->getPropertyValues(new \core_kernel_classes_Property(self::OLD_MEMBER_PROPERTY));
                foreach ($users as $userUri) {
                    if (GroupsService::singleton()->addUser($userUri, $group)) {
                        //$group->removePropertyValue(new \core_kernel_classes_Property(self::OLD_MEMBER_PROPERTY), $userUri);
                    }
                }
            }
            $this->setVersion('2.6.1');
        }

        if ($this->isBetween('2.6.1', '2.7')) {
            $this->setVersion('2.7');
        }

        if ($this->isVersion('2.7')) {
            OntologyUpdater::syncModels();
            $this->setVersion('2.7.1');
        }

        $this->skip('2.7.1', '3.0.0');
        // fix anonymous access
        if ($this->isVersion('3.0.0')) {
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, Api::class));
            $this->setVersion('3.0.1');
        }

        $this->skip('3.0.1', '6.5.1');
        
        //Updater files are deprecated. Please use migrations.
        //See: https://github.com/oat-sa/generis/wiki/Tao-Update-Process

        $this->setVersion($this->getExtension()->getManifest()->getVersion());
    }
}
