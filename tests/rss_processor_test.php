<?php
class rss_processor extends UnitTestCase {
	private $xmlfile;
	private $err;
	/**
	 * @var RSSProcessor
	 */
	private $pro;
	
	function SetUp(){
		$this->xmlfile = all_tests::tested_dir().'/tests/runnable/rss.xml';
		$this->pro = new RSSProcessor($this->xmlfile);
		$this->err = new Error(1,'Hallo',array());
		if(file_exists($this->xmlfile))unlink($this->xmlfile);
	}
	
	function tearDown(){
		@unlink($this->xmlfile);
	}

	private function content(){
		return file_get_contents($this->xmlfile);
	}
	
	function test_writing(){
		//insert 2 errors and test splitting
		$this->pro->notify_of_error($this->err);
		$this->pro->notify_of_error($this->err);
		
		$this->assertEqual(substr_count($this->content(),'<entry'),2);
	}
	
	function test_viewing(){
		//basic empty feed
		$this->pro->notify_of_error($this->err);
		
		$out = file_get_contents($this->xmlfile);
		
		$this->assert(new PatternExpectation('/author/'),$out);
		$this->assert(new PatternExpectation('/<feed/'),$out);
		$this->assert(new PatternExpectation('/<\?xml version=/'),$out);
		$this->assert(new PatternExpectation('/<\/feed>/'),$out);
		

		//with errors
		$e1 = new Error(1,'Eins',array());
		$e2 = new Error(1,'Zwei',array());
		$this->pro->notify_of_error($e1);
		$this->pro->notify_of_error($e2);
		$out = file_get_contents($this->xmlfile);
		
		$this->assert(new PatternExpectation('/feed/'),$out);
		$this->assert(new PatternExpectation('/entry/'),$out);
		$this->assert(new PatternExpectation('/Eins/'),$out);
		$this->assert(new PatternExpectation('/Zwei/'),$out);
		
		
		//max 10 entrys
		unlink($this->xmlfile);
		for($i=0;$i<23;$i++){
			$this->pro->notify_of_error($e1);
		}
		$this->assertEqual(substr_count($this->content(),'<entry'),RSSProcessor::MAX_ENTRIES);
	}
}
?>













