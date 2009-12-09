<?php
$todefine = array(
	'TAO_GROUP_CLASS' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'GENERIS_BOOLEAN'		=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>