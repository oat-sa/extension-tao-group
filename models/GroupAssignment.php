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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoGroups\models;

use oat\oatbox\user\User;
use oat\oatbox\service\ConfigurableService;
use core_kernel_classes_Resource;
use core_kernel_classes_Class;
use \core_kernel_classes_Property;
use oat\taoDelivery\model\AssignmentService;
use oat\taoDeliveryRdf\model\guest\GuestTestUser;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\AttemptServiceInterface;
use oat\taoDeliveryRdf\model\DeliveryContainerService;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoDeliveryRdf\model\AssignmentFactory;
use oat\taoGroups\helpers\DeliveryWidget;
use oat\taoDeliveryRdf\model\AssignmentWidgetAware;

/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class GroupAssignment extends ConfigurableService implements AssignmentService, AssignmentWidgetAware
{
    /**
     * Interface part
     */
    const PROPERTY_GROUP_DELIVERY = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries';

    const DISPLAY_ATTEMPTS_OPTION = 'display_attempts';

    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\AssignmentService::getAssignments()
     */
    public function getAssignments(User $user)
    {
        $assignments = array();
        foreach ($this->getAssignmentFactories($user) as $factory) {
            $assignments[] = $factory->toAssignment();
        }
        
        return $this->orderAssignments($assignments);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAssignmentFactories(User $user)
    {
        $candidates = $this->isDeliveryGuestUser($user)
            ? $this->getGuestAccessDeliveries()
            : $this->getDeliveryIdsByUser($user);
        
        $displayAttempts = ($this->hasOption(self::DISPLAY_ATTEMPTS_OPTION)) ? $this->getOption(self::DISPLAY_ATTEMPTS_OPTION) : true;
        $assignments = array();
        foreach ($candidates as $deliveryId) {
            $delivery = new \core_kernel_classes_Resource($deliveryId);
            $startable = $this->verifyTime($delivery) && $this->verifyToken($delivery, $user);
            $assignments[] = $this->getAssignmentFactory($delivery, $user, $startable, $displayAttempts);
        }
        return $assignments;
    }

    /**
     * @deprecated
     */
    public function getRuntime($deliveryId)
    {
        return $this->getServiceLocator()->get(RuntimeService::SERVICE_ID)->getRuntime($deliveryId);
    }
    
    
    /**
     * 
     * @param string $deliveryId
     * @return array identifiers of the users
     */
    public function getAssignedUsers($deliveryId)
    {
        $groupClass = GroupsService::singleton()->getRootClass();
        $groups = $groupClass->searchInstances(array(
            self::PROPERTY_GROUP_DELIVERY => $deliveryId
        ), array('recursive' => true, 'like' => false));
        
        $users = array();
        foreach ($groups as $group) {
            foreach (GroupsService::singleton()->getUsers($group) as $user) {
                $users[] = $user->getUri();
            }
        }
        return array_unique($users);
    }
    
    /**
     * Helpers
     */
    /**
     * @param core_kernel_classes_Resource $delivery
     */
    public function onDelete(core_kernel_classes_Resource $delivery)
    {
        $groupClass = GroupsService::singleton()->getRootClass();
        $assigned = $groupClass->searchInstances(array(
			self::PROPERTY_GROUP_DELIVERY => $delivery
        ), array('like' => false, 'recursive' => true));
        
        $assignationProperty = new core_kernel_classes_Property(self::PROPERTY_GROUP_DELIVERY);
        foreach ($assigned as $groupInstance) {
            $groupInstance->removePropertyValue($assignationProperty, $delivery);
        }
    }

    /**
     * @param User $user
     * @return array
     */
    public function getDeliveryIdsByUser(User $user)
    {
        $deliveryUris = array();
        // check if really available
        foreach (GroupsService::singleton()->getGroups($user) as $group) {
            foreach ($group->getPropertyValues(new \core_kernel_classes_Property(self::PROPERTY_GROUP_DELIVERY)) as $deliveryUri) {
                $candidate = new core_kernel_classes_Resource($deliveryUri);
                if (!$this->isUserExcluded($candidate, $user) && $candidate->exists()) {
                    $deliveryUris[$candidate->getUri()] = $candidate->getUri();
                }
            }
        }

        ksort($deliveryUris);
        return $deliveryUris;
    }

    /**
     * Check if a user is excluded from a delivery
     * @param core_kernel_classes_Resource $delivery
     * @param User $user the URI of the user to check
     * @return boolean true if excluded
     */
    protected function isUserExcluded(\core_kernel_classes_Resource $delivery, User $user){
        $excludedUsers = $delivery->getPropertyValues(new \core_kernel_classes_Property(DeliveryContainerService::PROPERTY_EXCLUDED_SUBJECTS));
        return in_array($user->getIdentifier(), $excludedUsers);
    }

    /**
     * Search for deliveries configured for guest access
     *
     * @return array
     */
    public function getGuestAccessDeliveries()
    {
        $class = new core_kernel_classes_Class(DeliveryAssemblyService::CLASS_URI);

        return $class->searchInstances(
            array(
                DeliveryContainerService::PROPERTY_ACCESS_SETTINGS => DeliveryAssemblyService::PROPERTY_DELIVERY_GUEST_ACCESS
            ),
            array('recursive' => true)
        );
    }

    /**
     * Check if current user is guest
     *
     * @param User $user
     * @return bool
     */
    public function isDeliveryGuestUser(User $user)
    {
        return ($user instanceof GuestTestUser);
    }

    /**
     * @param string $deliveryIdentifier
     * @param User $user
     * @return bool
     */
    public function isDeliveryExecutionAllowed($deliveryIdentifier, User $user)
    {
        $delivery = new \core_kernel_classes_Resource($deliveryIdentifier);
        return $this->verifyUserAssigned($delivery, $user)
            && $this->verifyTime($delivery)
            && $this->verifyToken($delivery, $user);
    }

    /**
     * @param core_kernel_classes_Resource $delivery
     * @param User $user
     * @return bool
     */
    protected function verifyUserAssigned(core_kernel_classes_Resource $delivery, User $user){
        $returnValue = false;
    
        //check for guest access mode
        if($this->isDeliveryGuestUser($user) && $this->hasDeliveryGuestAccess($delivery)){
            $returnValue = true;
        } else {
            $userGroups = GroupsService::singleton()->getGroups($user);
            $deliveryGroups = GroupsService::singleton()->getRootClass()->searchInstances(array(
				self::PROPERTY_GROUP_DELIVERY => $delivery->getUri()
            ), array(
                'like'=>false, 'recursive' => true
            ));
            $returnValue = count(array_intersect($userGroups, $deliveryGroups)) > 0 && !$this->isUserExcluded($delivery, $user);
        }
    
        return $returnValue;
    }
    
    /**
     * Check if delivery configured for guest access
     *
     * @param core_kernel_classes_Resource $delivery
     * @return bool
     * @throws \common_exception_InvalidArgumentType
     */
    protected function hasDeliveryGuestAccess(core_kernel_classes_Resource $delivery )
    {
        $returnValue = false;
    
        $properties = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(DeliveryContainerService::PROPERTY_ACCESS_SETTINGS ),
        ));
        $propAccessSettings = current($properties[DeliveryContainerService::PROPERTY_ACCESS_SETTINGS ]);
        $accessSetting = (!(is_object($propAccessSettings)) or ($propAccessSettings=="")) ? null : $propAccessSettings->getUri();
    
        if( !is_null($accessSetting) ){
            $returnValue = ($accessSetting === DeliveryAssemblyService::PROPERTY_DELIVERY_GUEST_ACCESS);
        }
    
        return $returnValue;
    }

    /**
     * @param core_kernel_classes_Resource $delivery
     * @param User $user
     * @return bool
     */
    protected function verifyToken(core_kernel_classes_Resource $delivery, User $user)
    {
        $propMaxExec = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(DeliveryContainerService::PROPERTY_MAX_EXEC));
        $maxExec = is_null($propMaxExec) ? 0 : $propMaxExec->literal;
        
        //check Tokens
        $usedTokens = count($this->getServiceLocator()->get(AttemptServiceInterface::SERVICE_ID)
            ->getAttempts($delivery->getUri(), $user));
    
        if (($maxExec != 0) && ($usedTokens >= $maxExec)) {
            \common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). "without tokens");
            return false;
        }
        return true;
    }

    /**
     * @param core_kernel_classes_Resource $delivery
     * @return bool
     */
    protected function verifyTime(core_kernel_classes_Resource $delivery)
    {
        $deliveryProps = $delivery->getPropertiesValues(array(
            DeliveryContainerService::PROPERTY_START,
            DeliveryContainerService::PROPERTY_END,
        ));
        
        $startExec = empty($deliveryProps[DeliveryContainerService::PROPERTY_START])
            ? null
            : (string)current($deliveryProps[DeliveryContainerService::PROPERTY_START]);
        $stopExec = empty($deliveryProps[DeliveryContainerService::PROPERTY_END])
            ? null
            : (string)current($deliveryProps[DeliveryContainerService::PROPERTY_END]);
        
        $startDate  =    date_create('@'.$startExec);
        $endDate    =    date_create('@'.$stopExec);
        if (!$this->areWeInRange($startDate, $endDate)) {
            \common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). " at the wrong date");
            return false;
        }
        return true;
    }
    
    /**
     * Check if the date are in range
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean true if in range
     */
    protected function areWeInRange($startDate, $endDate){
        return (empty($startDate) || date_create() >= $startDate)
        && (empty($endDate) || date_create() <= $endDate);
    }
    
    /**
     * Order Assignments of a given user.
     * 
     * By default, this method relies on the taoDelivery:DisplayOrder property
     * to order the assignments (Ascending order). However, implementers extending
     * the GroupAssignment class are encouraged to override this method if they need
     * another behaviour.
     * 
     * @param array $assignments An array of assignments.
     * @return array The $assignments array ordered.
     */
    protected function orderAssignments(array $assignments) {
        usort($assignments, function ($a, $b) {
            return $a->getDisplayOrder() - $b->getDisplayOrder();
        });
        
        return $assignments;
    }

    /**
     * @param core_kernel_classes_Resource $delivery
     * @param User $user
     * @param $startable
     * @param bool $displayAttempts
     * @return AssignmentFactory
     */
    protected function getAssignmentFactory(\core_kernel_classes_Resource $delivery, User $user, $startable, $displayAttempts = true)
    {
        $factory = new AssignmentFactory($delivery, $user, $startable, $displayAttempts);
        $factory->setServiceLocator($this->getServiceLocator());
        return $factory;
    }
    
    public function getAssignmentWidget(\core_kernel_classes_Resource $delivery)
    {
        $widget = new DeliveryWidget($delivery);
        return $widget;
    }
}
