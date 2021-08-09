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

require_once(JPATH_COMPONENT.'/assets/libs/Mailchimp.php'); 
require_once(JPATH_COMPONENT.'/assets/libs/cm.php');

use \DrewM\MailChimp\MailChimp;

/**
 * Class FormulariosController
 *
 * @since  1.6
 */
class FormulariosController extends JControllerLegacy
{
	function __construct()
    {
		parent::__construct();
    }

	public function sendForm()
	{
		$db 	= JFactory::getDbo();
     	$app    = JFactory::getApplication();

     	$params = JComponentHelper::getParams( 'com_formularios' );

		$newsletter     		= $params->get('newsletter', 0);
		$newsletter_apikey  	= $params->get('newsletter_apikey', '');
		$newsletter_listId  	= $params->get('newsletter_listId', '');
		$newsletter_clientId  	= $params->get('newsletter_clientId', '');

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
		    $captcha 		= $_POST['g-recaptcha-response'];
		 	$secretKey 		= $params->get('reCaptcha_secretkey');
			$ip 		    = $_SERVER['REMOTE_ADDR'];
		    $response	    = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
		    $responseKeys   = json_decode($response, true);
        }

        if($responseKeys['score'] >= 0.6 || $captchaEnabled == 0) {
		 	//recollim dades necessaries del formulari pare
		 	$db->setQuery('SELECT email, name FROM `#__formularios_forms` WHERE id = '.$data['type']);
		 	$row = $db->loadObject();

		 	//recollim tota la informació dels camps del formulari
		 	$db->setQuery('SELECT * FROM `#__formularios_fields` WHERE formId = '.$data['type'].' AND state = 1');
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
		 	unset($data['return'],$data['type'],$data['tos']);

		 	$db->setQuery('SELECT success_msg FROM `#__formularios_forms` WHERE id = '.$type);
		 	$success = $db->loadResult();
		 	$db->setQuery('SELECT error_msg FROM `#__formularios_forms` WHERE id = '.$type);
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
			$form->state    		= 1;
			$form->status    		= 0;
			$form->ordering    		= 0;
			$form->checked_out    	= 0;
			$form->checked_out_time = '0000-00-00 00:00:00';
			$form->created_by    	= 0;
			$save = $db->insertObject('#__formularios_stored', $form);

			if($send && $save) {

				//si l'opció newsletter es activa al backend i frontend i existeix el camp email, subscribim la persona
				if(isset($data['newsletter']) && $newsletter <> 0 && $notify != '') {
					//Mailchimp selected
					if($newsletter == 1) {
						$MailChimp = new MailChimp($newsletter_apikey);

						//FormulariosHelpersFormularios::customLog('ok:: '.$notify);

						$result = $MailChimp->post("lists/$newsletter_listId/members", [
							'email_address' => $notify,
							'status'        => 'subscribed',
						]);

						// if ($MailChimp->success()) {
						// 	FormulariosHelpersFormularios::customLog('ok:: '.$result);	
						// } else {
						// 	FormulariosHelpersFormularios::customLog($MailChimp->getLastError());
						// }
					}
					//Campaing MOnitor selected
					if($newsletter == 2) {
						$cm = new CampaignMonitor( $newsletter_apikey, $newsletter_clientId, null, $newsletter_listId );
						$cm->subscriberAdd($notify, '');
					}
				}

				$msg = JText::_($success);
				$type = 'success';
				//enviem confirmació si hi ha email
				if($notify != '') {
					$this->enviar($subject, $msg, $notify, $attach);
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

        $mailer->setSender( $mailfrom, $fromname );
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
