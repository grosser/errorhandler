<?php
/**
 * @see PEAR/Mail.php
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */

require_once('ErrorProcessor.class.php');

class MailProcessor extends ErrorProcessor {
	
	private $to;
	
	public function __construct($to_email){
		assert(is_string($to_email));
		$this->to = $to_email;
		
		require_once('TextRenderer.class.php');
		$this->set_renderer(new TextRenderer());
	}
	
	protected function render_error(Error $error){
		$mail = $this->create_mail();
      //$mail->headers()
		$body = $this->get_renderer()->render($error);
		$my_mail = 'no-reply@foo.bar';
		$hdrs = array(
			'From'    => $my_mail,
			'Return-Path'  => $my_mail,
			'Reply-To'  => 'ErrorHandler',
			'Subject' => $this->get_renderer()->errortype_to_word($error->get_type()),
		);
		$mail->send($this->to,$hdrs, $body);
	}
	
	protected function create_mail(){
		//include Mail
		@include_once 'Mail.php';
		//use the Autoloader to find PEAR/Mail
		if(!class_exists('Mail',true))return;
		
		return Mail::factory('mail');
	}
}
?>