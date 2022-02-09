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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$model  = $this->getModel();
$user 	= Factory::getUser();
$app  	= Factory::getApplication();
$formid = $app->input->get('formId', 1);
$step   = $app->input->get('step', 0);
$step > 0 ? $data   = $model->getFormData($step) : $data   = $model->getFormData($formid);
$url  	= Uri::current();

if($data->registered == 1 && $user->guest) {
	$returnurl = Route::_('index.php?option=com_users&view=login&return='.base64_encode($url), false);
	$app->enqueueMessage(Text::_('COM_FORMULARIOS_LOGIN_BEFORE'), 'notice');
	$app->redirect($returnurl);
}

$uri 				= base64_encode($url);
$captchaEnabled 	= $this->params->get('reCaptcha', 0);
$sitekey    		= $this->params->get('reCaptcha_sitekey', '');
$newsletter     	= $this->params->get('newsletter', 0);
$privacy			= $this->params->get('show_privacy', 1);
$btn_class			= $this->params->get('btn_class', 'primary');
$btn_align			= $this->params->get('btn_align', 'start');
$input_size			= $this->params->get('input_size', '');
$show_mandatory		= $this->params->get('show_mandatory', 1);
?>

<?php if($this->params->get('honeypot', 0) == 1) : ?>
<style>
#honeypot { position: absolute; left: -5000px; }
</style>
<?php endif; ?>

<section id="section-contact" style="background-color:#f2f2f2;">
	<div class="container py-5 form-page">

		<?php if($data->advanced == 1) : ?>
		<?php $count    = $model->getNumberOfForms($formid); ?>
		<div class="container mb-5">
			<ul class="step d-flex flex-nowrap">
				
				<?php for($i=0;$i<=$count;$i++) : ?>
				<li class="step-item <?= ($step-1 == $i || $step == 0) ? 'active' : ''; ?>">
					<a href="#!" class=""><?= Text::_('COM_FORMULARIOS_STEP'); ?> <?= $i; ?></a>
				</li>
				<?php endfor; ?>

			</ul>
		</div>
		<?php endif; ?>

		<div class="page-header text-center text-blue mb-5">
			<h1><?= Text::_($data->heading); ?></h1>
		</div>

		<div class="row">
			<?php if($data->subheading != '') : ?>
			<div class="col-md-12">
				<p class="section-header"><?= Text::_($data->subheading); ?></p>
			</div>
			<?php endif; ?>

			<?php if($this->params->get('map', 0) == 1 || $this->params->get('text', 0) == 1) : ?>
			<div class="col-12 col-md-6">
				<?php if($this->params->get('map', 0) == 1) : ?>
				<iframe src="<?= $this->params->get('map_url'); ?>" width="<?= $this->params->get('map_width'); ?>" height="<?= $this->params->get('map_height'); ?>" frameborder="0" style="border:0" allowfullscreen></iframe>
				<?php endif; ?>
				<?php if($this->params->get('text', 0) == 1) : ?>
				<?= $this->params->get('text_content'); ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="col-12 <?php if($this->params->get('map', 0) == 1 || $this->params->get('text', 0) == 1) : ?>col-md-6<?php endif; ?>">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="<?= JURI::root(); ?>index.php?option=com_formularios&task=<?= $data->advanced == 0 ? 'sendForm' : 'saveAdvancedForm'; ?>" method="post" role="form" id="contactForm" class="contactForm needs-validation" novalidate enctype="multipart/form-data">
						<input type="hidden" name="jform[return]" value="<?= $uri; ?>" />
						<input type="hidden" name="jform[formId]" value="<?= $formid; ?>" />
						<input type="hidden" name="jform[type]" value="<?= $formid; ?>" />
						<?php if($data->advanced == 1) : ?>
						<input type="hidden" name="jform[step]" value="<?= $step; ?>" />
						<?php endif; ?>
						<?php if($captchaEnabled == 1) : ?>
						<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
						<?php endif; ?>
						<?php if($this->params->get('honeypot', 0) == 1) : ?>
						<input type="text" id="honeypot" name="jform[honeypot]" value="" />
						<?php endif; ?>

						<div class="row">
						<?php foreach($model->getItem() as $item) : ?>
						<?php $item->field_required == 1 ? $required = 'required="true"' : $required = ''; ?>
						<?php $item->field_required == 1 ? $star = '*' : $star = ''; ?>
						<?php $item->field_readonly == 1 ? $readonly = 'readonly' : $readonly = ''; ?>
						<?php $item->field_disabled == 1 ? $disabled = 'disabled' : $disabled = ''; ?>

						<div class="col-<?= $item->field_column; ?>">
					  	<div class="form-group mb-3">

					  		<?php if($item->field_label != '' && $item->field_type != 'checkbox' && $item->field_type != 'radio' && $item->field_type != 'spacer') : ?>
							<label for="jform_<?= $item->field_name; ?>"><?= Text::_($item->field_label); ?>: <?= $star; ?></label>
							<?php endif; ?>

							<?php if($item->field_type == 'text' || $item->field_type == 'email') : ?>
							<input type="<?= $item->field_type; ?>" name="jform[<?= $item->field_name; ?>]" class="form-control<?= $input_size; ?>" id="jform_<?= $item->field_name; ?>" placeholder="<?= Text::_($item->field_hint); ?>" <?php if($item->field_type == 'email') : ?>data-rule="email"<?php endif; ?> <?= $required; ?> data-msg="<?= $item->field_msg; ?>" />
							
							<?php elseif($item->field_type == 'textarea') : ?>
							<textarea rows="10" name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> <?= $readonly; ?> <?= $disabled; ?> id="jform_<?= $item->field_uniqid; ?>" placeholder="<?= Text::_($item->field_hint); ?>" data-msg="<?= $item->field_msg; ?>"></textarea>

							<?php elseif($item->field_type == 'select') : ?>
							<select name="jform[<?= $item->field_name; ?>]" class="form-control" <?= $required; ?> <?= $readonly; ?> <?= $disabled; ?> id="jform_<?= $item->field_uniqid; ?>" data-msg="<?= $item->field_msg; ?>">
							<option value=""><?= Text::_('COM_FORMULARIOS_SELECT_OPTION'); ?></option>
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

							<?php if($item->field_type == 'spacer') : ?>
							<?= Text::_($item->field_label); ?>
							<?php endif; ?>

							<?php if($item->field_type == 'checkbox' || $item->field_type == 'radio') : ?>
								<div class="checkbox nopad mb-3">
									<label>
										<input name="<?= $item->field_name; ?>" <?= $disabled; ?> type="checkbox"> <small><?= Text::_($item->field_label); ?></small>
									</label>
						  		</div>
							<?php endif; ?>

							<div class="valid-feedback"><?= Text::_('COM_FORMULARIOS_VALIDATION_SUCCESS_MSG'); ?></div>
							<div class="invalid-feedback"><?= Text::_('COM_FORMULARIOS_VALIDATION_ERROR_MSG'); ?></div>
					  	</div>
					  	</div>

					  	<?php endforeach; ?>
						</div>

					  	<div class="col-12 text-<?= $btn_align; ?>">
						  	<?php if($privacy == 1) : ?>
							<div class="checkbox nopad mb-3">
								<label>
							  		<?php $link = JRoute::_('index.php?Itemid='.FormulariosHelpersFormularios::getPrivacyPolicy()); ?>
							  		<input name="jform[tos]" id="tos" value="0" onclick="this.value==0?this.value=1:this.value=0;" type="checkbox"> <small><?= Text::sprintf('COM_FORMULARIOS_TOS', $link); ?>.</small>
								</label>
						  	</div>
							<?php endif; ?>
							<?php if($newsletter <> 0) : ?>
								<div class="checkbox nopad mb-3">
									<label>
										<input name="jform[newsletter]" value="0" onclick="this.value==0?this.value=1:this.value=0;" type="checkbox"> <small><?= Text::_('COM_FORMULARIOS_NEWSLETTER_LBL'); ?></small>
									</label>
						  		</div>
							<?php endif; ?>
						  	<button type="submit" <?php if($privacy == 1) : ?>disabled="true"<?php endif; ?> id="submit" class="btn btn-<?= $btn_class; ?>"><?= ($data->advanced == 1 && $data->redirect > 0) ? Text::_('COM_FORMULARIOS_NEXT') : Text::_('ENVIAR'); ?></button>
					  	</div>
					</form>
					<div class="my-4"><?= FormulariosHelpersFormularios::getFooter(); ?></div>
				</div>
			</div>
			<!-- ./span12 -->
		</div>

	</div>
</section>
<script>
(function() {
	'use strict'

	//count seconds
	var counter = 0;
	window.setInterval(function(){
	counter++;
	},1000);

	<?php if($privacy == 1) : ?>
	var tos = document.getElementById('tos');
	tos.addEventListener("click",function(e){
		if(tos.checked) {
			document.getElementById('submit').removeAttribute("disabled");
			<?php if($captchaEnabled == 1) : ?>
			grecaptcha.ready(function() {
				grecaptcha.execute('<?= $sitekey; ?>', {action: 'contact'})
				.then(function(token) {
				var recaptchaResponse = document.getElementById('g-recaptcha-response');
				recaptchaResponse.value = token;
				});
			});
			<?php endif; ?>
		} else {
			document.getElementById('submit').setAttribute("disabled", "true");
		}
	});
	<?php endif; ?>

	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.querySelectorAll('.needs-validation')

	// Loop over them and prevent submission
	Array.prototype.slice.call(forms)
	.forEach(function (form) {
		form.addEventListener('submit', function (event) {
		if (!form.checkValidity()) {
			event.preventDefault()
			event.stopPropagation()
		}

		form.classList.add('was-validated')
		}, false)
	})
})()
</script>