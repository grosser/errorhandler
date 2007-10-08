<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */

require_once('ErrorProcessor.class.php');

class DirectProcessor extends ErrorProcessor {
	public function __construct(){
		$sapi = php_sapi_name();
		
		switch($sapi){
			case 'cli': 
				//cosole
				require_once('TextRenderer.class.php');
				$this->set_renderer(new TextRenderer());
			break;
			default:
				require_once('HtmlRenderer.class.php');
				$this->set_renderer(new HtmlRenderer());
		}
	}
	
	protected function render_error($error){
		print $this->get_renderer()->render($error);
	}
}
?>