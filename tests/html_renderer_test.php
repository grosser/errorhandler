<?php
class html_renderer_test extends UnitTestCase {
	function SetUp(){
		$this->renderer = new TestHtmlRenderer();
	}
	
	function test_everything_to_html(){
		//--------string
		$out = $this->renderer->test_everything_to_html(array('<&>xxxx'));
		$real_out = array(htmlentities('<&>xxxx'));
		$this->assertEqual($out,$real_out);
		
		//--------array
		$out = $this->renderer->test_everything_to_html(array(
			'a<>'=>array('<&>xxxx'),
			'b<>'=>array('xxxx<&>'),
			'c'=>array(1),
		));
		$real_out = array(
			'a<>'=>array(htmlentities('<&>xxxx')),
			'b<>'=>array(htmlentities('xxxx<&>')),
			'c'=>array(1),
		);
		$this->assertEqual($out,$real_out);
	}
	
	function test_output(){
		//error without backtrace
		$error = new Error(1,'Nachricht',array());
		$out = $this->renderer->render($error);
		$this->assert(new PatternExpectation('/ERROR:/'),$out);
		$this->assert(new PatternExpectation('/Nachricht/'),$out);
	}
}
?>























