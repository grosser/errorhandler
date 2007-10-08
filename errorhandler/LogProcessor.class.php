<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('Exc/FileNotFoundExc.class.php');
require_once('TextRenderer.class.php');

class LogProcessor extends ErrorProcessor{
	
	public function __construct($file=null){
		if($file) $this->set_log_file($file,false);
		$this->set_renderer(new TextRenderer());
	}
	
	protected function render_error($error){
		$date = @date("Y.m.d,H:i:S");
		$out = $date . "\n" . $this->get_renderer()->render($error);
		
		$this->add_content($out);
	}

	protected function add_content($content){
		assert(is_string($content));
		$success = @file_put_contents($this->get_log_file(),$content,FILE_APPEND);
	}
	
	//--------log_file
	private $log_file;
		
	public function get_log_file(){
		if(!isset($this->log_file)){
			throw new Exception('No Logfile set for ' . __CLASS__);
		}
		return $this->log_file;
	}
	
	const TEST_FILE = true;
	
	/**
	 * Set the file to write to
	 *
	 * @param string $log_file -- path
	 * @param Bool $with_test -- test if file is writeable
	 */
	public function set_log_file($log_file,$with_test=self::TEST_FILE){
		assert(is_string($log_file));
		
		if($with_test == self::TEST_FILE){
			$cannot_write_to_file = is_dir($log_file) || !file_exists(dirname($log_file));
			if($cannot_write_to_file) throw new FileNotFoundExc($log_file);
		}
		$this->log_file = $log_file;
	}
	//--------END log_file
}
?>