<?php

/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
class ErrorProcessor {
	public function notify_of_error(Error $error){
		print $this->render_error($error);
	}
	
	protected function render_error($error){
		$this->get_renderer()->render($error);
	}
	
	//--------renderer
	private $renderer;
		
	/**
	 * @return ErrorRenderer
	 */
	protected function get_renderer(){
		return $this->renderer;
	}
	public function set_renderer(ErrorRenderer $renderer){
		$this->renderer = $renderer;
	}
	//--------END renderer
}
?>