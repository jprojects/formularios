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
$app  	= JFactory::getApplication();
$formid = $app->input->get('formId', 1);
$url  	= JURI::current();
$lang 	= JFactory::getLanguage()->getTag();
$lg   	= explode('-', $lang);

if($model->isLogin($formid) && $user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url), false);
	$app->redirect($returnurl, JText::_('COM_FORMULARIOS_LOGIN_BEFORE'));
}

$uri 	= base64_encode($url);
$params = JComponentHelper::getParams( 'com_formularios' );
$captchaEnabled = $params->get('reCaptcha', 0);
$sitekey    	= $params->get('reCaptcha_sitekey', '');
?>

<script>
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

grecaptcha.ready(function() {
	grecaptcha.execute('<?= $sitekey; ?>', {action: 'contact'})
	.then(function(token) {
	var recaptchaResponse = document.getElementById('g-recaptcha-response');
	recaptchaResponse.value = token;
	});
});
</script>

<?php if($params->get('honeypot', 0) == 1) : ?>
<style>
#honeypot { position: absolute; left: -5000px; }
</style>
<?php endif; ?>

<section id="section-contact" class="jpfblock">
	<div class="form-page">
		<div class="page-header">
			<h2><?= JText::_($model->getFormData('heading', $formid)); ?></h2>
		</div>
		<div class="row">
			<?php if($model->getFormData('subheading', $formid) != '') : ?>
			<div class="col-md-12">
				<p class="section-header"><?= JText::_($model->getFormData('subheading', $formid)); ?></p>
			</div>
			<?php endif; ?>

			<?php if($params->get('map', 0) == 1 || $params->get('text', 0) == 1) : ?>
			<div class="col-xs-12 col-md-6">
				<?php if($params->get('map', 0) == 1) : ?>
				<iframe src="<?= $params->get('map_url'); ?>" width="<?= $params->get('map_width'); ?>" height="<?= $params->get('map_height'); ?>" frameborder="0" style="border:0" allowfullscreen></iframe>
				<?php endif; ?>
				<?php if($params->get('text', 0) == 1) : ?>
				<?= $params->get('text_content'); ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="col-xs-12 <?php if($params->get('map', 0) == 1 || $params->get('text', 0) == 1) : ?>col-md-6<?php else: ?>col-md-8 col-md-offset-2<?php endif; ?>">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="<?= JURI::root(); ?>index.php?option=com_formularios&task=sendForm" method="post" role="form" id="contactForm" class="contactForm" enctype="multipart/form-data">
						<?php if($captchaEnabled == 1) : ?>
						<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
						<?php endif; ?>
						<p class="section-header"><?= JText::_('COM_FORMULARIOS_MANDATORY_FIELDS'); ?></p>
						<input type="hidden" name="jform[return]" value="<?= $uri; ?>" />
						<input type="hidden" name="jform[type]" value="<?= $formid; ?>" />

						<?php if($params->get('honeypot', 0) == 1) : ?>
						<input type="text" id="honeypot" name="jform[honeypot]" value="" />
						<?php endif; ?>


						<?php foreach($model->getItem() as $item) : ?>
						<?php $item->field_required == 1 ? $required = 'required="true"' : $required = ''; ?>
						<?php $item->field_required == 1 ? $star = '*' : $star = ''; ?>
						<div class="col-xs-<?= $item->field_column; ?>">
					  	<div class="form-group mb-3">

					  		<?php if($item->field_label != '') : ?>
							<label for="jform_<?= $item->field_name; ?>"><?= JText::_($item->field_label); ?>: <?= $star; ?></label>
							<?php endif; ?>

							<?php if($item->field_type == 'text' || $item->field_type == 'email') : ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" class="form-control" id="jform_<?= $item->field_name; ?>" placeholder="<?= JText::_($item->field_hint); ?>" <?php if($item->field_type == 'email') : ?>data-rule="email"<?php endif; ?> <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />
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
					  	</div>
					  	</div>
					  	<?php endforeach; ?>
					  	<div class="col-xs-12">
							<div class="checkbox nopad mb-3">
								<label>
							  		<?php $link = JRoute::_('index.php?Itemid='.FormulariosHelpersFormularios::getPrivacyPolicy()); ?>
							  		<input class="tos" type="checkbox"> <small><?= JText::sprintf('COM_FORMULARIOS_TOS', $link); ?>.</small>
								</label>
						  	</div>
						  	<button type="submit" disabled="true" class="btn btn-primary submit"><?= JText::_('JSUBMIT'); ?></button>
					  	</div>
					</form>
					<div class="my-4"><?= FormulariosHelpersFormularios::getFooter(); ?></div>
				</div>
			</div>
			<!-- ./span12 -->
		</div>

	</div>
</section>
