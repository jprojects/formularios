<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Formularios
 * @author     aficat <kim@aficat.com>
 * @copyright  2018 aficat
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class FormulariosController
 *
 * @since  1.6
 */
class FormulariosController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app  = JFactory::getApplication();
        $view = $app->input->getCmd('view', 'forms');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function sendForm()
	{
		$db 	= JFactory::getDbo();
     	$app    = JFactory::getApplication();
     	
     	$params = JComponentHelper::getParams( 'com_formularios' );
     	
     	$data 	= $app->input->post->get('jform', array(), 'array');
     	$save	= $send = false;
     	$return = base64_decode($data['return']);
     	
     	$captchaEnabled = $params->get('reCaptcha', 0);
     	
     	if($captchaEnabled == 1) {
		 	if(isset($_POST['g-recaptcha-response'])){
		      $captcha = $_POST['g-recaptcha-response'];
		    }
		    if(!$captcha){
		      	$msg = JText::_('COM_FORMULARIOS_CAPTCHA_FAIL');
			   	$type = 'error';
				$this->setRedirect($return, $msg, $type);
				return false;
		    }     	
     	
		 	$secretKey = $params->get('reCaptcha_secretkey');
		    $ip = $_SERVER['REMOTE_ADDR'];
		    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
		    $responseKeys = json_decode($response,true);
        }
        
        if(intval($responseKeys["success"]) == 1 || $captchaEnabled == 0) {
		 	//recollim dades necessaries
		 	$db->setQuery('SELECT email, name FROM #__formularios_forms WHERE id = '.$data['type']);
		 	$row = $db->loadObject();
		 	
		 	$type = $data['type'];
		 	
		 	//send email
		 	unset($data['return']);
		 	unset($data['type']);
		 	$subject = 'Ferrer: Nou email rebut desde el formulari '.$row->name;
		 	$body      = "<p>Aquestes son les dades rebudes desde el formular.</p>";
		 	foreach($data as $k => $v) {
		 		$body .= $k.": ".$v."<br>";
		 	}
			
		 	$send = $this->enviar($subject, $body, $row->email);
		 	
		 	//insert body into database
		 	$form 				= new stdClass();
			$form->formId 		= $type;
			$form->data    		= date('Y-m-d H:i:s');
			$form->message    	= $body;
			$save = $db->insertObject('#__formularios_stored', $form);
		
			if($send && $save) {
				$msg = JText::_('COM_FORMULARIOS_SUCCESS_SEND_MSG');
				$type = 'info';
			} else {
				$msg = JText::_('COM_FORMULARIOS_ERROR_SEND_MSG');
				$type = 'error';
			}
		} else {
			$msg = JText::_('COM_FORMULARIOS_CAPTCHA_FAIL');
		   	$type = 'error';
		}
		
		$this->setRedirect($return, $msg, $type);
			
	}
	
	public function enviar($subject, $body, $email, $attach='') 
	{
		$mailer 	= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');	
	
		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;	
		
        $mailer->setSender( $sender );
        $mailer->addRecipient( $email );
        $mailer->setSubject( $subject );
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody( $body );   
        
        if(attach != '') {
        	$mailer->addAttachment(JPATH_ROOT.'/'.$attach.'.pdf');     
        }
        
		return $mailer->Send();			
	}
}
