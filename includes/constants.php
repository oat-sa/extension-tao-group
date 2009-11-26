<?php
$todefine = array(
	'TAO_GROUP_CLASS' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>