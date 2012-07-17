<?php
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';

//get the test into each extensions
$tests = TestRunner::getTests(array('taoGroups'));

//create the test sutie
$testSuite = new TestSuite('TAO Group unit tests');
foreach($tests as $testCase){
	$testSuite->addFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}

require_once  PHPCOVERAGE_HOME. "/CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";
//run the unit test suite
$includePaths = array(ROOT_PATH."taoGroups/actions", ROOT_PATH.'taoGroups/models',ROOT_PATH.'taoGroups/helpers');
$excludePaths = array();
$covReporter = new HtmlCoverageReporter("Code Coverage Report taoGroups", "", PHPCOVERAGE_REPORTS."/taoGroups");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
$testSuite->run($reporter);
$cov->stopInstrumentation();
$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'/taoGroups_coverage.txt');

?>