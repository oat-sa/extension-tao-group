<?php

/**
 * Groups Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoGroups_actions_Groups extends tao_actions_TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoGroups_models_classes_GroupsService::singleton();
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected group from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $group
	 */
	protected function getCurrentInstance()
	{
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
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass()
	{
		return $this->service->getGroupClass();
	}
	
/*
 * controller actions
 */
	
	/**
	 * Edit a group class
	 * @return void
	 */
	public function editGroupClass()
	{
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getGroupClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit group class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * Edit a group instance
	 * @return void
	 */
	public function editGroup()
	{
		$clazz = $this->getCurrentClass();
		$group = $this->getCurrentInstance();

		$formContainer = new tao_actions_form_Instance($clazz, $group);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$group = $this->service->bindProperties($group, $myForm->getValues());
				
				$this->setData('message', __('Group saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($group->uriResource));
		
		$relatedSubjects = tao_helpers_Uri::encodeArray($this->service->getRelatedSubjects($group), tao_helpers_Uri::ENCODE_ARRAY_VALUES, true, true);
		
		$this->setData('relatedSubjects', json_encode(array_values($relatedSubjects)));
		
		$relatedDeliveries = tao_helpers_Uri::encodeArray($this->service->getRelatedDeliveries($group), tao_helpers_Uri::ENCODE_ARRAY_VALUES, true, true);
		$this->setData('relatedDeliveries', json_encode($relatedDeliveries));
		
		$this->setData('formTitle', 'Edit group');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	
	/**
	 * Add a group subclass
	 * @return void
	 */
	public function addGroupClass()
	{
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
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteGroup($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Get the data to populate the tree of group's subjects
	 * @return void
	 */
	public function getMembers()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array(
			'chunk' => false
		);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the group related subjects
	 * @return void
	 */
	public function saveMembers()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$members = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($members, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedSubjects($group, $members)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of group's deliveries
	 * @return void
	 */
	public function getDeliveries()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the group related deliveries
	 * @return void
	 */
	public function saveDeliveries()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$deliveries = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($deliveries, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedDeliveries($group, $deliveries)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	
}
?>