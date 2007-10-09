<?php
class rss_processor extends UnitTestCase {
	private $cachefile;
	private $err;
	/**
	 * @var RSSProcessor
	 */
	private $pro;
	
	function SetUp(){
		$this->pro = new RSSProcessor();
		$this->err = new Error(1,'Hallo',array());
		$this->cachefile = all_tests::tested_dir().'/tests/runnable/rss.cache';
		if(file_exists($this->cachefile))unlink($this->cachefile);
	}
	
	function tearDown(){
		@unlink($this->cachefile);
	}

	function test_writing(){
		$this->pro->set_cache_file($this->cachefile);
		
		//insert 2 errors and test splitting
		$this->pro->notify_of_error($this->err);
		$this->pro->notify_of_error($this->err);
		
		$content = file_get_contents($this->cachefile);
		$content = split(RSSProcessor::ENTRY_SEPERATOR,$content);
		
		$this->assertEqual(count($content),3);
	}
	
	function test_viewing(){
		$this->pro->set_cache_file($this->cachefile);
		
		//basic empty feed
		$out = $this->pro->rss();
		$this->assert(new PatternExpectation('/author/'),$out);
		$this->assert(new PatternExpectation('/<feed/'),$out);
		$this->assert(new PatternExpectation('/<\?xml version=/'),$out);
		$this->assert(new PatternExpectation('/<\/feed>/'),$out);
		$this->assert(new NoPatternExpectation('/entry/'),$out);
		

		//with errors
		$e1 = new Error(1,'Eins',array());
		$e2 = new Error(1,'Zwei',array());
		$this->pro->notify_of_error($e1);
		$this->pro->notify_of_error($e2);
		$out = $this->pro->rss();
		
		$this->assert(new PatternExpectation('/feed/'),$out);
		$this->assert(new PatternExpectation('/entry/'),$out);
		$this->assert(new PatternExpectation('/Eins/'),$out);
		$this->assert(new PatternExpectation('/Zwei/'),$out);
	}
}
?>













