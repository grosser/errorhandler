<?php
class rss_renderer_test extends UnitTestCase {
	function SetUp(){
		$this->renderer = new RSSRenderer();
	}
	
	function test_output(){
		//error without backtrace
		$error = new Error(1,'Nachricht',array());
		$out = $this->renderer->render($error);
		
		$this->assert(new PatternExpectation('/<entry>/'),$out);
		$this->assert(new PatternExpectation('/<description>/'),$out);
		$this->assert(new PatternExpectation('/Nachricht/'),$out);
		$this->assert(new NoPatternExpectation('/Called from/'),$out);
		$this->assert(new PatternExpectation('/<\/description>/'),$out);
		$this->assert(new PatternExpectation('/<\/entry>/'),$out);
	}
}
?>























