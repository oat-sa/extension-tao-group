<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants end

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */
class taoGroups_models_classes_GroupsService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level group class
     *
     * @access protected
     * @var Class
     */
    protected $groupClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB begin
		
		parent::__construct();
		$this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB end
    }

    /**
     * get a group subclass by uri. 
     * If the uri is not set, it returns the group class (the top level class.
     * If the uri don't reference a group subclass, it returns null
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getGroupClass($uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AE8 begin
		
		if(empty($uri) && !is_null($this->groupClass)){
			$returnValue = $this->groupClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isGroupClass($clazz)){
				$returnValue = $clazz;
			}
		}
		
        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AE8 end

        return $returnValue;
    }

    /**
     * get a group instance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier usually the test label or the ressource URI
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getGroup($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D5 begin
		
		if(is_null($clazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				$type = $resource->getUniquePropertyValue(new core_kernel_classes_Property( RDF_TYPE ));
				$clazz = new core_kernel_classes_Class($type->uriResource);
			}
			catch(Exception $e){}
		}
		if(is_null($clazz)){
			$clazz = $this->groupClass;
		}
		if($this->isGroupClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D5 end

        return $returnValue;
    }

    /**
     * create a group instance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @deprecated
     * @param  string label
     * @param  ContainerCollection members
     * @param  ContainerCollection tests
     * @return core_kernel_classes_Resource
     */
    public function createGroup($label,  core_kernel_classes_ContainerCollection $members,  core_kernel_classes_ContainerCollection $tests)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017CD begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017CD end

        return $returnValue;
    }

    /**
     * subclass the Group class or one of it's subclass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createGroupClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B11 begin
		
		if(is_null($clazz)){
			$clazz = $this->groupClass;
		}
		
		if($this->isGroupClass($clazz)){
		
			$groupClass = $this->createSubClass($clazz, $label);
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $groupClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $groupClass;
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B11 end

        return $returnValue;
    }

    /**
     * delete a group instance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource group
     * @return boolean
     */
    public function deleteGroup( core_kernel_classes_Resource $group)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 begin
		
		if(!is_null($group)){
			$returnValue = $group->delete();
		}
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 end

        return (bool) $returnValue;
    }

    /**
     * delete a group class or sublcass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteGroupClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B0D begin
		
		if(!is_null($clazz)){
			if($this->isGroupClass($clazz) && $clazz->uriResource != $this->groupClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B0D end

        return (bool) $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of the Group Class
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isGroupClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA begin
		
		if($clazz->uriResource == $this->groupClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->groupClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
		
        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA end

        return (bool) $returnValue;
    }

    /**
     * get the list of subjects linked to the group in parameter
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource group
     * @return array
     */
    public function getRelatedSubjects( core_kernel_classes_Resource $group)
    {
        $returnValue = array();

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 begin
		
		if(!is_null($group)){
			$subjects = $group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP));
			
			if(count($subjects) > 0){
				$subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
				$subjectSubClasses = array();
				foreach($subjectClass->getSubClasses(true) as $subjectSubClass){
					$subjectSubClasses[] = $subjectSubClass->uriResource;
				}
				foreach($subjects as $subjectUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($subjectUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $subjectSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $subjectUri;
				}
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 end

        return (array) $returnValue;
    }

    /**
     * define the list of subjects composing a group
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource group
     * @param  array subjects
     * @return boolean
     */
    public function setRelatedSubjects( core_kernel_classes_Resource $group, $subjects = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D48 begin
		
		if(!is_null($group)){
			
			$memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
			
			$group->removePropertyValues($memberProp);
			$done = 0;
			foreach($subjects as $subject){
				if($group->setPropertyValue($memberProp, $subject)){
					$done++;
				}
			}
			if($done == count($subjects)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D48 end

        return (bool) $returnValue;
    }

    /**
     * get the list of deliveries linked to the group in parameter
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource group
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $group)
    {
        $returnValue = array();

        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED4 begin
		
		if(!is_null($group)){
			$deliveries = $group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP));
		
			$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
			$deliverySubClasses = array();
			foreach($deliveryClass->getSubClasses(true) as $deliverySubClass){
				$deliverySubClasses[] = $deliverySubClass->uriResource;
			}
			foreach($deliveries as $deliveryUri){
				$clazz = $this->getClass(new core_kernel_classes_Resource($deliveryUri));
				if(!is_null($clazz)){
					if(in_array($clazz->uriResource, $deliverySubClasses)){
						$returnValue[] = $clazz->uriResource;
					}
				}
				$returnValue[] = $deliveryUri;
			}
		}
		
        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED4 end

        return (array) $returnValue;
    }

    /**
     * define a list of deliveries linked to the group in parameter
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource group
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $group, $deliveries = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED7 begin
		
		if(!is_null($group)){
			
			$deliveriesProp = new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$group->removePropertyValues($deliveriesProp);
			$done = 0;
			foreach($deliveries as $delivery){
				if($group->setPropertyValue($deliveriesProp, $delivery)){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED7 end

        return (bool) $returnValue;
    }

} /* end of class taoGroups_models_classes_GroupsService */

?>