<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 15.12.2009, 14:14:33 with ArgoUML PHP module 
 * (last revised $Date: 2009-04-11 21:57:46 +0200 (Sat, 11 Apr 2009) $)
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants end

/**
 * Short description of class taoGroups_models_classes_GroupsService
 *
 * @access public
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package taoGroups
 * @subpackage models_classes
 */
class taoGroups_models_classes_GroupsService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute groupClass
     *
     * @access protected
     * @var Class
     */
    protected $groupClass = null;

    /**
     * Short description of attribute groupsOntologies
     *
     * @access protected
     * @var array
     */
    protected $groupsOntologies = array('http://www.tao.lu/Ontologies/TAOGroup.rdf', 'http://www.tao.lu/Ontologies/TAOSubject.rdf','http://www.tao.lu/Ontologies/TAOTest.rdf');

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB begin
		
		parent::__construct();
		$this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		$this->loadOntologies($this->groupsOntologies);
		
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB end
    }

    /**
     * Short description of method getGroupClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method getGroup
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string identifier usually the test label or the ressource URI
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getGroup($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D5 begin
		
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
     * Short description of method getGroups
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  array options
     * @return core_kernel_classes_ContainerCollection
     */
    public function getGroups($options)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001809 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001809 end

        return $returnValue;
    }

    /**
     * Short description of method createGroup
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method createGroupClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
					$propertyName . ' ' . $label .' subject property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $groupClass;
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B11 end

        return $returnValue;
    }

    /**
     * Short description of method deleteGroup
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method deleteGroupClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method isGroupClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method getRelatedSubjects
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource group
     * @return array
     */
    public function getRelatedSubjects( core_kernel_classes_Resource $group)
    {
        $returnValue = array();

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 begin
		
		if(!is_null($group)){
			$returnValue = $group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP));
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setRelatedSubjects
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method getRelatedTests
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource group
     * @return array
     */
    public function getRelatedTests( core_kernel_classes_Resource $group)
    {
        $returnValue = array();

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D53 begin
		
		if(!is_null($group)){
			$returnValue = $group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_TESTS_PROP));
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D53 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setRelatedTests
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource group
     * @param  array tests
     * @return boolean
     */
    public function setRelatedTests( core_kernel_classes_Resource $group, $tests = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D57 begin
		
		if(!is_null($group)){
			
			$testProp = new core_kernel_classes_Property(TAO_GROUP_TESTS_PROP);
			
			$group->removePropertyValues($testProp);
			$done = 0;
			foreach($tests as $test){
				if($group->setPropertyValue($testProp, $test)){
					$done++;
				}
			}
			if($done == count($tests)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D57 end

        return (bool) $returnValue;
    }

} /* end of class taoGroups_models_classes_GroupsService */

?>