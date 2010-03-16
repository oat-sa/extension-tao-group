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
	
}
?>