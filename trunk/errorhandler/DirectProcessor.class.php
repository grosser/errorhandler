<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */

require_once('ErrorProcessor.class.php');

class DirectProcessor extends ErrorProcessor {
	/**
	 * html or text ?
	 */
	private $output_type;
	
	public function __construct(){
		$sapi = php_sapi_name();
		
		switch($sapi){
			case 'cli': 
				//cosole
				$this->output_type='text';
				require_once('TextRenderer.class.php');
				$this->set_renderer(new TextRenderer());
			break;
			default:
				$this->output_type='html';
				require_once('HtmlRenderer.class.php');
				$this->set_renderer(new HtmlRenderer());
		}
	}
	
	protected function render_error($error){
		if($this->output_type=='html'){
			if(!ini_get('display_errors'))return;
			
			//try to get the output on screen (href="<?=$xx ERROR")
			print "\"></div>";
			
			print ini_get('error_prepend_string');
			print $this->get_renderer()->render($error);
			print ini_get('error_append_string');
		}
		else {
			print $this->get_renderer()->render($error);
		}
	}
}
?>