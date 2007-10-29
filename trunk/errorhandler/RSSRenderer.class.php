<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('HtmlRenderer.class.php');

class RSSRenderer extends HTMLRenderer{
	
	/**
	 * Render a backtrace to string
	 */
	protected function render_error(Error $error,$topic='Error'){
		$out = '<entry>';
		$out .= $this->render_title($topic,$error->get_message());
		$out .= '<summary><![CDATA[';
		$out .= $this->render_backtrace($error->get_backtrace());
		$out .= ']]></summary>';
		$out .= $this->render_globals();
		$out .= "<id>".rand()."</id>";
		$out .= '</entry>';

		return $out;
	}
	
	
	/**
	 * Render the title
	 */
	protected function render_title($topic,$message){
		return "<title> $topic:" . str_replace("\n","<br>",$message) . '</title>';	
	}
}
?>