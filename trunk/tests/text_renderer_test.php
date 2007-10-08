<?php
class text_renderer_test extends UnitTestCase {

	function setUp(){
		$this->renderer = new TextRenderer();
	}
	
	function test_output(){
		//error without backtrace
		$error = new Error(1,'Nachricht',array());
		$out = $this->renderer->render($error);
		$real_out = "-->>Error type ERROR<<--\nNachricht\n\n";
		$this->assertEqual($out,$real_out);
	}
}
?>























