<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 * Test the group management 
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage test
 */
class GroupsTestCase extends UnitTestCase {
	
	/**
	 * @var taoGroups_models_classes_GroupsService
	 */
	protected $groupsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoGroups_models_classes_GroupsService::__construct
	 */
	public function testService(){
		
		$groupsService = tao_models_classes_ServiceFactory::get('Groups');
		$this->assertIsA($groupsService, 'tao_models_classes_Service');
		$this->assertIsA($groupsService, 'taoGroups_models_classes_GroupsService');
		
		$this->groupsService = $groupsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the group class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_GROUP_CLASS'));
		$groupClass = $this->groupsService->getGroupClass();
		$this->assertIsA($groupClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_GROUP_CLASS, $groupClass->uriResource);
		
		//create a subclass
		$subGroupClassLabel = 'subGroup class';
		$subGroupClass = $this->groupsService->createSubClass($groupClass, $subGroupClassLabel);
		$this->assertIsA($subGroupClass, 'core_kernel_classes_Class');
		$this->assertEqual($subGroupClassLabel, $subGroupClass->getLabel());
		$this->assertTrue($this->groupsService->isGroupClass($subGroupClass));
		
		//create instance of Group
		$groupInstanceLabel = 'group instance';
		$groupInstance = $this->groupsService->createInstance($groupClass, $groupInstanceLabel);
		$this->assertIsA($groupInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($groupInstanceLabel, $groupInstance->getLabel());
		
		//create instance of subGroup
		$subGroupInstanceLabel = 'subGroup instance';
		$subGroupInstance = $this->groupsService->createInstance($subGroupClass);
		
		$this->assertTrue(defined('RDFS_LABEL'));
		$subGroupInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subGroupInstance->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $subGroupInstanceLabel);
		$this->assertIsA($subGroupInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subGroupInstanceLabel, $subGroupInstance->getLabel());
		
		$subGroupInstanceLabel2 = 'my sub group instance';
		$subGroupInstance->setLabel($subGroupInstanceLabel2);
		$this->assertEqual($subGroupInstanceLabel2, $subGroupInstance->getLabel());
		
		
		//delete group instance
		$this->assertTrue($groupInstance->delete());
		
		//delete subclass and check if the instance is deleted
		$subGroupInstanceUri = $subGroupInstance->uriResource;
		$this->assertNotNull($this->groupsService->getGroup($subGroupInstanceUri));
		$this->assertTrue($subGroupInstance->delete());
		$this->assertNull($this->groupsService->getGroup($subGroupInstanceUri));
		
		$this->assertTrue($subGroupClass->delete());
	}
	
}
?>