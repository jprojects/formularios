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
$user   = JFactory::getUser();
$formid = JFactory::getApplication()->input->get('formId', 1);
$lang   = JFactory::getLanguage()->getTag();
$lg     = explode('-', $lang);

if($model->isLogin($formid) && $user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()).'&Itemid=465', false);
	$app->redirect($returnurl, JText::_('COM_FORMULARIOS_LOGIN_BEFORE'));
}

$uri 	= base64_encode(JFactory::getURI()->toString());
$params = JComponentHelper::getParams( 'com_formularios' );
$captchaEnabled = $params->get('reCaptcha', 0);
$sitekey = $params->get('reCaptcha_sitekey');
?>

<script>
jQuery(document).ready(function() {
jQuery('.jump').change(function() {
	//document.location.href='?formId='+jQuery(this).val();
	document.location.href='index.php?option=com_formularios&view=form&formId='+jQuery(this).val()+'&Itemid=412';
});
});
<?php if($captchaEnabled == 0) : ?>
jQuery(document).ready(function() {
//count seconds
var counter = 0;
window.setInterval(function(){
  counter++;
},1000);

jQuery('.tos').click(function() {
	if(jQuery(this).is(':checked') && counter > 3) {
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

<style>
#section-contact { background-image: url(<?= JURI::root(); ?>images/logo-vermell-<?= $lg[0]; ?>.jpg); }
@media (max-width: 480px) {
    #section-contact { background-image: none; }
}
<?php if($params->get('honeypot', 0) == 1) : ?>
#honeypot { position: absolute; left: -5000px; }
<?php endif; ?>
</style>

<section id="section-contact" class="section appear clearfix">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section-header" <?php if($model->getFormText($formid) != '') : ?>style="text-align:left;"<?php endif; ?>>
					<h2 class="section-heading"><?= JText::_($model->getFormData('heading', $formid)); ?></h2>
				</div>
			</div>
		</div>
		<div class="row">
			<?php if($model->getFormData('subheading', $formid) != '') : ?>
			<div class="col-md-12 hidden-xs hidden-sm text-center">
				<p class="section-header"><?= JText::_($model->getFormData('subheading', $formid)); ?></p>
			</div>
			<?php endif; ?>
			<?php if($params->get('map', 0) == 1) : ?>
			<div class="col-xs-12 col-md-6">
				<iframe src="<?= $params->get('map_url'); ?>" width="<?= $params->get('map_width'); ?>" height="<?= $params->get('map_height'); ?>" frameborder="0" style="border:0" allowfullscreen></iframe>
			</div>
			<?php endif; ?>
			<div class="col-xs-12 <?php if($params->get('map', 0) == 1) : ?>col-md-6<?php else: ?>col-md-8 col-md-offset-2<?php endif; ?>">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="<?= JURI::root(); ?>index.php?option=com_formularios&task=sendForm" method="post" role="form" class="contactForm" enctype="multipart/form-data">
						<p class="section-header"><?= JText::_('COM_FORMULARIOS_MANDATORY_FIELDS'); ?></p>
						<input type="hidden" name="jform[return]" value="<?= $uri; ?>" />

						<?php if($params->get('honeypot', 0) == 1) : ?>
						<input type="text" id="honeypot" name="jform[honeypot]" value="" />
						<?php endif; ?>

						<div class="form-group">
							<label for="jform_type">Escull un tipus de formulari</label>
							<select name="jform[type]" id="jform_type" class="form-control jump">
								<option value=""><?= JText::_('COM_FORMULARIOS_SELECT_OPTION'); ?></option>
								<?php foreach($model->getForms() as $form) : ?>
								<option value="<?= $form->id; ?>" <?= $formid == $form->id ? 'selected' : ''; ?>><?= $form->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<?php foreach($model->getItem() as $item) : ?>
						<?php $item->field_required == 1 ? $required = 'required="true"' : $required = ''; ?>
						<?php $item->field_required == 1 ? $star = '*' : $star = ''; ?>
						<div class="col-xs-<?= $item->field_column; ?>">
					  	<div class="form-group">

							<?php if($item->field_label != '') : ?>
							<label for="jform_<?= $item->field_name; ?>"><?= JText::_($item->field_label); ?>: <?= $star; ?></label>
							<?php endif; ?>

							<?php if($item->field_type == 'text' || $item->field_type == 'email') : ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" class="form-control" id="jform_<?= $item->field_name; ?>" placeholder="<?= JText::_($item->field_hint); ?>" <?php if($item->field_type == 'email') : ?>data-rule="email"<?php endif; ?> <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />
							<?php elseif($item->field_type == 'textarea') : ?>
							<textarea rows="10" name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> id="jform_<?= $item->field_uniqid; ?>" placeholder="<?= JText::_($item->field_hint); ?>" data-msg="<?= $item->field_msg; ?>"></textarea>

							<?php elseif($item->field_type == 'file') : ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" id="jform_<?= $item->field_name; ?>" placeholder="<?= $item->field_hint; ?>" <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />

							<?php elseif($item->field_type == 'select') : ?>
							<select name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> id="jform_<?= $item->field_uniqid; ?>" data-msg="<?= $item->field_msg; ?>">
							<option value=""><?= JText::_('COM_FORMULARIOS_SELECT_OPTION'); ?></option>
							<?php $values = explode(',', $item->field_values); ?>
							<?php foreach($values as $value) : ?>
							<?php if (strpos($value, '|') === false) : ?>
							<option value="<?= $value; ?>"><?= $value; ?></option>
							<?php else : ?>
							<?php $parts = explode('|', $value); ?>
							<option value="<?= $parts[0]; ?>"><?= $parts[1]; ?></option>
							<?php endif; ?>
							<?php endforeach; ?>
							</select>
							<?php endif; ?>

							<div class="validation"></div>
					  	</div>
					  	</div>
					  	<?php endforeach; ?>
					  	<div class="col-xs-12">
							<div class="checkbox nopad">
								<label>
							  		<?php $link = JRoute::_('index.php?Itemid='.FormulariosHelpersFormularios::getPrivacyPolicy()); ?>
							  		<input class="tos" type="checkbox"> <small><?= JText::sprintf('COM_FORMULARIOS_TOS', $link); ?>.</small>
								</label>
						  	</div>
						  	<?php if($captchaEnabled == 1) : ?>
						  	<div class="form-group">
				   		 		<div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?= $sitekey; ?>"></div>
							</div>
							<?php endif; ?>
						  	<button type="submit" disabled="true" class="line-btn green submit"><?= JText::_('JSUBMIT'); ?></button>
					  	</div>
					</form>

				</div>
			</div>
			<!-- ./span12 -->
		</div>

	</div>
</section>
