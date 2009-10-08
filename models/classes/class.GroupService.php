<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoGroups\models\classes\class.GroupService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.09.2009, 15:14:24 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D2-constants end

/**
 * Short description of class taoGroups_models_classes_GroupService
 *
 * @access public
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */
class taoGroups_models_classes_GroupService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createGroup
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
     * Short description of method getGroups
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
     * Short description of method getGroup
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  mixed identifier usually the test label or the ressource URI
     * @return core_kernel_classes_Resource
     */
    public function getGroup( mixed $identifier)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D5 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D5 end

        return $returnValue;
    }

    /**
     * Short description of method deleteGroup
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  Resource group
     * @return boolean
     */
    public function deleteGroup( core_kernel_classes_Resource $group)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 end

        return (bool) $returnValue;
    }

} /* end of class taoGroups_models_classes_GroupService */

?>