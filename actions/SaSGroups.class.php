<?php
/**
 * SaSGroups Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSGroups extends Groups {

    
    
    /**
     * @see Groups::__construct()
     */
    public function __construct() {
        $this->setSessionAttribute('currentExtension', 'taoGroups');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
		parent::__construct();
    }
    	
		
	/**
     * @see TaoModule::setView()
     */
    public function setView($identifier, $useMetaExtensionView = false) {
		if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		parent::setView('sas.tpl', true);
    }
	
	/**
	 * Render the tree to select the group related subjects 
	 * @return void
	 */
	public function selectSubjects(){
		$this->setData('relatedSubjects', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getRelatedSubjects($this->getCurrentInstance()))));
		$this->setView('subjects.tpl');
	}
	
	/**
	 * Render the tree to select the group related tests 
	 * @return void
	 */
	public function selectTests(){
		$this->setData('relatedTests', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getRelatedTests($this->getCurrentInstance()))));
		$this->setView('tests.tpl');
	}
}
?>