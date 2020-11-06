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
$user 	= JFactory::getUser();
$formid = JFactory::getApplication()->input->get('formId', 1);
$app  	= JFactory::getApplication();
$url  	= JFactory::getURI()->toString();
$lang 	= JFactory::getLanguage()->getTag();
$lg   	= explode('-', $lang);

if($model->isLogin($formid) && $user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url), false);
	$app->redirect($returnurl, JText::_('COM_FORMULARIOS_LOGIN_BEFORE'));
}

$uri 			= base64_encode($url);
$params 		= JComponentHelper::getParams( 'com_formularios' );
$inline 		= $params->get('inline_inputs', 0);
$show_mandatory = $params->get('show_mandatory', 0);
$show_privacy 	= $params->get('show_privacy', 0);
$captchaEnabled = $params->get('reCaptcha', 0);
$sitekey 		= $params->get('reCaptcha_sitekey');
?>

<script>
jQuery(document).ready(function() {
//count seconds
var counter = 0;
window.setInterval(function(){
  counter++;
},1000);
<?php if($show_privacy == 1) : ?>
jQuery('.tos').click(function() {
	if(jQuery(this).is(':checked') && counter > 3) {
        jQuery('.submit').removeAttr('disabled');
    } else {
        jQuery('.submit').attr('disabled', 'disabled');
    }
});
<?php endif; ?>
<?php if($captchaEnabled == 0) : ?>
grecaptcha.ready(function () {
	grecaptcha.execute('<?= $sitekey; ?>', { action: 'contact' }).then(function (token) {
		var recaptchaResponse = document.getElementById('recaptchaResponse');
		recaptchaResponse.value = token;
	});
});
<?php endif; ?>
});
</script>

<style>
#section-contact {
	margin-top: 50px;
}
.inline { border: none; border-bottom: 1px solid #000; }
<?php if($params->get('honeypot', 0) == 1) : ?>
#honeypot { position: absolute; left: -5000px; }
<?php endif; ?>
</style>

<section id="section-contact">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="page-header mb-5">
					<h1><?= JText::_($model->getFormData('heading', $formid)); ?></h1>
				</div>
			</div>
		</div>
		<div class="row">
			<?php if($model->getFormData('subheading', $formid) != '') : ?>
			<div class="col-12 text-center">
				<p class="section-header"><?= JText::_($model->getFormData('subheading', $formid)); ?></p>
			</div>
			<?php endif; ?>

			<?php if($params->get('map', 0) == 1 || $params->get('text', 0) == 1) : ?>
			<div class="col-12 col-md-6">
				<?php if($params->get('map', 0) == 1) : ?>
				<iframe src="<?= $params->get('map_url'); ?>" width="<?= $params->get('map_width'); ?>" height="<?= $params->get('map_height'); ?>" frameborder="0" style="border:0" allowfullscreen></iframe>
				<?php endif; ?>
				<?php if($params->get('text', 0) == 1) : ?>
				<?= $params->get('text_content'); ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="col-12 <?php if($params->get('map', 0) == 1 || $params->get('text', 0) == 1) : ?>col-md-6<?php else: ?>col-md-10 mx-auto<?php endif; ?>">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="<?= JURI::root(); ?>index.php?option=com_formularios&task=sendForm" method="post" role="form" class="contactForm" enctype="multipart/form-data">
						<?php if($show_mandatory == 1) : ?><p class="section-header"><?= JText::_('COM_FORMULARIOS_MANDATORY_FIELDS'); ?></p><?php endif; ?>
						<input type="hidden" name="jform[return]" value="<?= $uri; ?>" />
						<input type="hidden" name="jform[type]" value="<?= $formid; ?>" />

						<?php if($params->get('honeypot', 0) == 1) : ?>
						<input type="text" id="honeypot" name="jform[honeypot]" value="" />
						<?php endif; ?>


						<?php foreach($model->getItem() as $item) : ?>
						<?php $item->field_required == 1 ? $required = 'required="true"' : $required = ''; ?>
						<?php $item->field_required == 1 ? $star = '*' : $star = ''; ?>
						<div class="col-12 col-sm-<?= $item->field_column; ?>">
						<div class="form-group <?php if($inline == 1) : ?>row<?php endif; ?>">

					  		<?php if($item->field_label != '') : ?>
							<label <?php if($inline == 1) : ?>class="col-sm-2 col-form-label"<?php endif; ?> for="jform_<?= $item->field_name; ?>"><b><?= JText::_($item->field_label); ?>: <?= $star; ?></b></label>
							<?php endif; ?>

							<?php if($item->field_type == 'text' || $item->field_type == 'email') : ?>
							<?php if($inline == 1) : ?><div class="col-sm-10"><?php endif; ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" class="form-control <?php if($inline == 1) : ?>inline mb-3<?php endif; ?>" id="jform_<?= $item->field_name; ?>" placeholder="<?= JText::_($item->field_hint); ?>" <?php if($item->field_type == 'email') : ?>data-rule="email"<?php endif; ?> <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />
							<?php elseif($item->field_type == 'textarea') : ?>
							<textarea rows="10" name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> id="jform_<?= $item->field_uniqid; ?>" placeholder="<?= JText::_($item->field_hint); ?>" data-msg="<?= $item->field_msg; ?>"></textarea>

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
						<?php if($inline == 1) : ?></div><?php endif; ?>
					  	</div>
					  	</div>
					  	<?php endforeach; ?>

					  	<div class="col-12">

						  	<?php if($show_privacy == 1) : ?>
							<div class="checkbox nopad">
								<label>
							  		<?php $link = JRoute::_('index.php?Itemid='.FormulariosHelpersFormularios::getPrivacyPolicy()); ?>
							  		<input class="tos" type="checkbox"> <small><?= JText::sprintf('COM_FORMULARIOS_TOS', $link); ?>.</small>
								</label>
						  	</div>
						  	<?php endif; ?>

							<?php if($params->get('comercial', 0) == 1) : ?>
							<div class="checkbox nopad">
								<label>
							  		<input class="consent" type="checkbox"> <small><?= JText::_('COM_FORMULARIOS_CONSENT'); ?>.</small>
								</label>
						  	</div>
							<?php endif; ?>

						  	<?php if($captchaEnabled == 1) : ?>
						  	<div class="form-group">
							  <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
							</div>
							<?php endif; ?>

						  	<button type="submit" <?php if($show_privacy == 1) : ?>disabled="true"<?php endif; ?> class="btn btn-dark px-5 submit"><?= JText::_('JSUBMIT'); ?></button>
					  	</div>
					</form>
					<div class="my-4"><?= FormulariosHelpersFormularios::getFooter(); ?></div>
				</div>
			</div>
			<!-- ./span12 -->
		</div>

	</div>
</section>
