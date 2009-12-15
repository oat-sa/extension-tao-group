<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Groups Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Groups extends TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Groups');
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected group from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $group
	 */
	private function getCurrentGroup(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$group = $this->service->getGroup($uri, 'uri', $clazz);
		if(is_null($group)){
			throw new Exception("No group found for the uri {$uri}");
		}
		
		return $group;
	}
	
/*
 * controller actions
 */

	/**
	 * Main action
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the group tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getGroups(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		} 
		echo json_encode( $this->service->toTree( $this->service->getGroupClass(), true, true, $highlightUri));
	}
	
	/**
	 * Edit a group class
	 * @see tao_helpers_form_GenerisFormFactory::classEditor
	 * @return void
	 */
	public function editGroupClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getGroupClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Groups', 'index');
			}
		}
		$this->setData('formTitle', 'Edit group class');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a group instance
	 * @see tao_helpers_form_GenerisFormFactory::instanceEditor
	 * @return void
	 */
	public function editGroup(){
		$clazz = $this->getCurrentClass();
		$group = $this->getCurrentGroup();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $group);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$group = $this->service->bindProperties($group, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($group->uriResource));
				$this->setData('message', 'Group saved');
				$this->setData('reload', true);
				$this->forward('Groups', 'index');
			}
		}
		
		$this->setData('formTitle', 'Edit group');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	/**
	 * Add a group instance
	 * @return void
	 */
	public function addGroup(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$group = $this->service->createInstance($clazz);
		if(!is_null($group) && $group instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $group->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($group->uriResource)
			));
		}
	}
	
	/**
	 * Add a group subclass
	 * @return void
	 */
	public function addGroupClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createGroupClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a group or a group class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteGroup($this->getCurrentGroup());
		}
		else{
			$deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a group instance
	 * @return void
	 */
	public function cloneGroup(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$group = $this->getCurrentGroup();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($group->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($group->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * get the list data: all taoObjects children except the TAO_GROUP class
	 * @return void
	 */
	public function getLists(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		return json_encode(
			$this->getListData(array(
				TAO_GROUP_CLASS
			))
		);
	}
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( $this->service->getGroupClass(), true, true, ''));
	}
	
	public function saveSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		echo json_encode(array('saved'	=> $saved));
	}
	
	public function getTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( $this->service->getGroupClass(), true, true, ''));
	}
	
	public function saveTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		echo json_encode(array('saved'	=> $saved));
	}
	
	public function getMetaData(){
		throw new Exception("Not yet implemented");
	}
	
	public function saveComment(){
		throw new Exception("Not yet implemented");
	}
	
}
?>