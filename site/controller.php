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
     	$attach = array();
     	$files  = array();
     	$notify = '';
     	
     	if($params->get('honeypot', 0) == 1) {
			if($data['honeypot'] !== "") {
				return false;
			}		
		}
     	
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
		 	//recollim dades necessaries del formulari pare
		 	$db->setQuery('SELECT email, name FROM #__formularios_forms WHERE id = '.$data['type']);
		 	$row = $db->loadObject();
		 	
		 	//recollim tota la informació dels camps del formulari
		 	$db->setQuery('SELECT * FROM #__formularios_fields WHERE formId = '.$data['type'].' AND state = 1');
		 	$fields = $db->loadObjectList();		 
		 	
		 	$type = $data['type'];
		 	
		 	
		 	foreach($fields as $field) { 
		 		//comprovem si hi han camps de tipus file
		 		if($field->field_type == 'file') {
		 			
				 	$files[] = $field->field_name;
				 	unset($data[$field->field_name]);				 	
				 	
				 	if(count($files)) {
				 		$filename = $this->upload($field->field_name);	
				 		$attach[] = JPATH_ROOT."/tmp/".$filename;	 		
				 	}
			 	}			 	
			 	//comprovem si hi ha camp tipus mail
			 	if($field->field_type == 'email') {
			 		$notify .= $data[$field->field_name];
			 	}
		 	}
		 	
		 	//enviem l'email
		 	unset($data['return']);
		 	unset($data['type']);
		 	
		 	$db->setQuery('SELECT success_msg FROM #__formularios_forms WHERE id = '.$type);
		 	$success = $db->loadResult();
		 	$db->setQuery('SELECT error_msg FROM #__formularios_forms WHERE id = '.$type);
		 	$error = $db->loadResult();
		 	
		 	$subject = $app->getCfg('sitename').': Nou email rebut desde el formulari '.JText::_($row->name);
		 	$body    = JText::_($success)."<p>Aquestes son les dades rebudes desde el formulari.</p>";
		 	foreach($data as $k => $v) {
		 		if($v != '') {
		 			$body .= $k.": ".$v."<br>";
		 		}
		 	}
			
		 	$send = $this->enviar($subject, $body, $row->email, $attach);
		 	//$send = $this->enviar($subject, $body, 'kim@aficat.com', $attach);		 			 		
		 	
		 	//insertem el missatge a la base de dades
		 	$form 					= new stdClass();
			$form->formId 			= $type;
			$form->data_missatge  	= date('Y-m-d H:i:s');
			$form->message    		= $body;
			$save = $db->insertObject('#__formularios_stored', $form);
		
			if($send && $save) {				
				$msg = JText::_($success);
				$type = 'info';
				//enviem confirmació si hi ha email
				if($notify != '') {
					$this->enviar($subject, $success, $notify, $attach);
				}
			} else {				
				$msg = JText::_($error);
				$type = 'error';
			}
		} else {
			$msg = JText::_('COM_FORMULARIOS_CAPTCHA_FAIL');
		   	$type = 'error';
		}
		
		//si hem pujat arxius els esborrem perque ja estan enviats
	 	if(count($attach)) {
	 		foreach($attach as $att) { 
	 			unlink($att);
	 		}
	 	}
		
		$this->setRedirect($return, $msg, $type);
			
	}
	
	public function enviar($subject, $body, $email, $attach=array()) 
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
        
        if(count($attach)) {
        	foreach($attach as $att) {
        		$mailer->addAttachment($att);
        	}     
        }
        
		return $mailer->Send();			
	}
	
	public function upload($fieldname)
	{   
		$jinput  = JFactory::getApplication()->input;
        $file    = $jinput->files->get('jform');  
       	$allowed = array('pdf', 'xlsm', 'xls', 'csv', 'doc', 'docx', 'xlsx', 'odt', 'jpg', 'png', 'jpeg');

    	jimport('joomla.filesystem.file');
     
    	$filename = JFile::makeSafe($file[$fieldname]['name']);

    	$src  = $file[$fieldname]['tmp_name'];
    	$dest = JPATH_ROOT."/tmp/".$filename;
    	$extension = strtolower(JFile::getExt($filename)); 

    	if ( in_array($extension, $allowed) ) {
       		JFile::upload($src, $dest);
       		return $filename;
    	} else {
    		return false;
    	}
	}
}
