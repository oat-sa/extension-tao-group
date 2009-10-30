<?php
/**
 * the groups module provide the actions to manage groups
 */
class Groups extends Module {

	/**
	 * @var taoGroups_models_classes_GroupsService
	 */
	protected $service = null;

	/**
	 * constructor
	 * initialize services 
	 */
	public function __construct(){
		$this->service = tao_models_classes_ServiceFactory::get('Groups');
	}
	
/*
 * controller actions
 */

	/**
	 * Main action
	 * @return void
	 */
	public function index(){
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
		$clazz = $this->getCurrentGroupClass();
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($clazz, $this->service->getGroupClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$classValues = array();
				$propertyValues = array();
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classKey =  tao_helpers_Uri::decode(str_replace('class_', '', $key));
						$classValues[$classKey] =  tao_helpers_Uri::decode($value);
					}
					if(preg_match("/^property_/", $key)){
						$key = str_replace('property_', '', $key);
						$propNum = substr($key, 0, 1 );
						$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $key));
						$propertyValues[$propNum][$propKey] = tao_helpers_Uri::decode($value);
					}
				}
				$clazz = $this->service->bindProperties($clazz, $classValues);
				foreach($propertyValues as $propNum => $properties){
					$this->service->bindProperties(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])), $properties);
				}
				if($clazz instanceof core_kernel_classes_Class){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'group class saved');
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
		$clazz = $this->getCurrentGroupClass();
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
		$this->setView('form.tpl');
	}
	
	/**
	 * Add a group instance
	 * @return void
	 */
	public function addGroup(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentGroupClass();
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
		$clazz = $this->service->createGroupClass($this->getCurrentGroupClass());
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
			$deleted = $this->service->deleteGroupClass($this->getCurrentGroupClass());
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
		$clazz = $this->getCurrentGroupClass();
		
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
	
	/*
	 * TODO
	 */
	
	 public function import(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	 public function export(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
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
		
		$clazz = $this->getCurrentGroupClass();
		$group = $this->service->getGroup($uri, 'uri', $clazz);
		if(is_null($group)){
			throw new Exception("No group found for the uri {$uri}");
		}
		
		return $group;
	}
	
	/**
	 * get the selected group class from the current context (from the classUri parameter in the request)
	 * @return core_kernel_classes_Class $clazz
	 */
	private function getCurrentGroupClass(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = new core_kernel_classes_Class($classUri);
		if(is_null($clazz)){
			throw new Exception("No class found for the uri {$classUri}");
		}
		
		return $clazz;
	}
}
?>