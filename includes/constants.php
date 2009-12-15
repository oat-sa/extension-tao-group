<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$todefine = array(
	'TAO_GROUP_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_GROUP_MEMBERS_PROP'=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Members',
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_GROUP_TESTS_PROP'	=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests',
	'TAO_TEST_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'GENERIS_BOOLEAN'		=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>