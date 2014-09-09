<?php
require_once dirname(__FILE__) . '/../../tao/test/RestTestCase.php';

class RestGroupsTest extends RestTestCase
{
    public function serviceProvider(){
        return array(
            array('taoGroups/Api')
        );
    }
}

?>