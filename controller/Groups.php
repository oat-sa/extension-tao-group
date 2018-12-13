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
 *               2013-2018 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoGroups\controller;

use \common_ext_ExtensionsManager;
use oat\tao\model\resources\ResourceWatcher;
use \tao_actions_SaSModule;
use \tao_helpers_Uri;
use \tao_helpers_form_GenerisTreeForm;
use \tao_models_classes_dataBinding_GenerisFormDataBinder;
use oat\taoGroups\models\GroupsService;
use oat\taoDeliveryRdf\helper\DeliveryWidget;

/**
 * This Module aims at managing the Group class and its instances.
 *
 * @author Bertrand Chevrier, <bertrand@taotesting.com>
 * @package taoGroups

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Groups extends tao_actions_SaSModule
{

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_SaSModule::getClassService()
	 */
	protected function getClassService()
	{
		return GroupsService::singleton();
	}

	/**
	 * Edit a group instance
	 * @return void
	 */
	public function editGroup()
	{
        $this->defaultData();

		$clazz = $this->getCurrentClass();
		$group = $this->getCurrentInstance();

		$formContainer = new \tao_actions_form_Instance($clazz, $group);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){

				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($group);
				$group = $binder->bind($myForm->getValues());

		        $this->setData("selectNode", tao_helpers_Uri::encode($group->getUri()));
				$this->setData('message', __('Group saved'));
				$this->setData('reload', true);
			}
		}

		$memberProperty = $this->getProperty(GroupsService::PROPERTY_MEMBERS_URI);
		$memberForm = tao_helpers_form_GenerisTreeForm::buildReverseTree($group, $memberProperty);
		$memberForm->setData('title',	__('Select group test takers'));
		$this->setData('memberForm', $memberForm->render());

		if ($this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID)->isEnabled('taoDeliveryRdf')) {
		    $this->setData('deliveryForm', DeliveryWidget::renderDeliveryTree($group));
		}
        $updatedAt = $this->getServiceLocator()->get(ResourceWatcher::SERVICE_ID)->getUpdatedAt($group);
		$this->setData('updatedAt', $updatedAt);
		$this->setData('formTitle', __('Edit group'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}

    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveResource()
     * @requiresRight uri WRITE
     */
    public function moveResource()
    {
        return parent::moveResource();
    }
    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveAll()
     * @requiresRight ids WRITE
     */
    public function moveAll()
    {
        return parent::moveAll();
    }
}
