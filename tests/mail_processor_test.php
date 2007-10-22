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
		$this->pro->mocker->add_return('create_mail',$this);
		$this->pro->notify_of_error(new Error(1,'testing mail',array()));
		$this->assertEqual($this->pro->mocker->get_count('create_mail'),1);
	}
	
	/**
	 * mocked function call
	 */
	function send($to,$headers,$body){
		$this->assertEqual($to,'xxx@xxx.xx');
		$this->assertEqual(substr_count($body,'testing mail'),1);
	}
}
?>













