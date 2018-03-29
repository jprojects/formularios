<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Formularios
 * @author     aficat <kim@aficat.com>
 * @copyright  2018 aficat
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
 
 // No direct access to this file
defined('_JEXEC') or die('Restricted access');
$model  = $this->getModel();
$user = JFactory::getUser();
$formid = JFactory::getApplication()->input->get('formId', 1);
$app = JFactory::getApplication();
$url = JFactory::getURI()->toString();

if($model->isLogin($formid) && $user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url), false);
	$app->redirect($returnurl, JText::_('COM_FORMULARIOS_LOGIN_BEFORE'));
}

$uri 	= base64_encode($url);
$params = JComponentHelper::getParams( 'com_formularios' );
$captchaEnabled = $params->get('reCaptcha', 0);
$sitekey = $params->get('reCaptcha_sitekey');
?>

<script>
<?php if($captchaEnabled == 0) : ?>
jQuery(document).ready(function() {
jQuery('.tos').click(function() {
	if(jQuery(this).is(':checked')) {  
        jQuery('.submit').removeAttr('disabled');  
    } else {  
        jQuery('.submit').attr('disabled', 'disabled');  
    } 
});
});
<?php else : ?>
var resolved = false;
function recaptchaCallback() {
    var resolved = true;
    if(resolved == true) {  
	    jQuery('.submit').removeAttr('disabled');  
	} else {  
	    jQuery('.submit').attr('disabled', 'disabled');  
	}
};
<?php endif; ?>
</script>

<section id="section-contact" class="section appear clearfix">
	<div class="container">			
		<div class="row">
			<div class="col-md-offset-3 col-md-6">
				<div class="section-header">
					<h2 class="section-heading"><?= $model->getFormTitle($formid); ?></h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="/index.php?option=com_formularios&task=sendForm" method="post" role="form" class="contactForm">
						<input type="hidden" name="jform[return]" value="<?= $uri; ?>" />
						<input type="hidden" name="jform[type]" value="<?= $formid; ?>" />
						
						<?php foreach($model->getItem() as $item) : ?>
						<?php $item->field_required == 1 ? $required = 'required="true"' : $required = ''; ?>
						<?php $item->field_required == 1 ? $star = '*' : $star = ''; ?>
					  	<div class="form-group">
							<label for="jform_<?= $item->field_name; ?>"><?= $item->field_label; ?>: <?= $star; ?></label>
							
							<?php if($item->field_type == 'text' || $item->field_type == 'email') : ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" class="form-control" id="jform_<?= $item->field_name; ?>" placeholder="<?= $item->field_hint; ?>" <?php if($item->field_type == 'email') : ?>data-rule="email"<?php endif; ?> <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />
							<?php elseif($item->field_type == 'textarea') : ?>
							<textarea name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> id="jform_<?= $item->field_uniqid; ?>" placeholder="<?= $item->field_hint; ?>" data-msg="<?= $item->field_msg; ?>"></textarea>
							
							<?php elseif($item->field_type == 'select') : ?>
							<select name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> id="jform_<?= $item->field_uniqid; ?>" data-msg="<?= $item->field_msg; ?>">
							<option value=""><?= JText::_('COM_FORMULARIOS_SELECT_OPTION'); ?></option>
							<?php $values = explode(',', $item->field_values); ?>
							<?php foreach($values as $value) : ?>
							<option value="<?= $value; ?>"><?= $value; ?></option>
							<?php endforeach; ?>
							</select>
							<?php endif; ?>
							
							<div class="validation"></div>
					  	</div>
					  	<?php endforeach; ?>
						<div class="checkbox nopad">			  	
							<label>
						  		<input class="tos" type="checkbox"> <small><?= JText::sprintf('COM_FORMULARIOS_TOS', '#'); ?>.</small>
							</label>
					  	</div>
					  	<?php if($captchaEnabled == 1) : ?>
					  	<div class="form-group">
			   		 		<div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?= $sitekey; ?>"></div>
						</div>
						<?php endif; ?>
					  	<button type="submit" disabled="true" class="line-btn green submit">ENVIAR</button>
					</form>

				</div>
			</div>
			<!-- ./span12 -->
		</div>
		
	</div>
</section>
<div class="col-md-4 hidden-xs hidden-sm" style="z-index:10;position:absolute;right:0;top:0;">
	<img src="components/com_formularios/assets/images/90anys.png">
</div>
