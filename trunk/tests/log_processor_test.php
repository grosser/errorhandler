<?php
class suit_name extends UnitTestCase {
	private $logfile;
	private $err;
	private $pro;
	
	function SetUp(){
		$this->pro = new LogProcessor();
		$this->err = new Error(1,'Hallo',array());
		$this->logfile = all_tests::tested_dir().'/tests/runnable/test.log';
		if(file_exists($this->logfile))unlink($this->logfile);
	}
	
	function tearDown(){
		@unlink($this->logfile);
	}
	
	function test_writing(){
		//--------writing to nonthing
		try {
			$this->pro->notify_of_error($this->err);
			$this->fail('No Exception!');
		} catch (Exception $irgnored){}
		
		//--------set wrong dir
		try {
			$this->pro->set_log_file('ARsdfd/log.txt');
			$this->fail('No Exception!');
		} catch (Exception $ignored) {}
		
		//--------set wrong dir but ignore
		$this->pro->set_log_file('ARsdfd/log.txt',false);
		
		//-------- writing to non-existent file
		$this->pro->set_log_file($this->logfile);
		$this->pro->notify_of_error($this->err);
		
		$out = file_get_contents($this->logfile);
		$this->assertFalse(empty($out));
	}
}
?>