<?php
class mail_processor extends UnitTestCase {
	/**
	 * @var MailProcessor
	 */
	private $pro;
	
	function SetUp(){
		$this->pro = new MockMailProcessor('xxx@xxx.xx');
		ini_set('include_path',ini_get('include_path').':'.all_tests::$conf['pear_path']);
	}
	
	function test_create(){
		$mail = $this->pro->mock_create_mail();
		$this->assertEqual(get_class($mail),'Mail_mail');
	}
	
	function test_send(){
		
		
		
	}
}
?>













