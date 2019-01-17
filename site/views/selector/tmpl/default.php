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
$model  	= $this->getModel();
$user 		= JFactory::getUser();
$app 		= JFactory::getApplication();
$url 		= JFactory::getURI()->toString();
$menu   	= $app->getMenu();
$Itemid   	= $menu->getActive()->id;
$lang 		= JFactory::getLanguage()->getTag();
$lg   		= explode('-', $lang);

$params = JComponentHelper::getParams( 'com_formularios' );
?>

<style>
#section-contact { 
	margin-top: 50px; 
}
#section-contact { background-image: url(<?= JURI::root(); ?>images/logo-vermell-<?= $lg[0]; ?>.jpg); }
/*.error { width: 100%; height: 15px; color: #e3001a; }*/
@media (max-width: 480px) {
    #section-contact { background-image: none; }
}
</style>

<section id="section-contact" class="section appear clearfix">
	<div class="container">			
		<div class="row">		
			<div class="col-md-12">
				<div class="section-header">
					<h2 class="section-heading"><?= JText::_('COM_FORMULARIOS_WELCOME'); ?></h2>
				</div>
			</div>			
		</div>
		<div class="row">
			
			<div class="col-md-8 col-md-offset-2">
				<div class="cform" id="contact-form">
					<div id="sendmessage">
						 <!-- message -->
					</div>
					<form action="/index.php?option=com_formularios" method="get" role="form" class="contactForm">
						<input type="hidden" name="option" value="com_formularios" />
						<input type="hidden" name="view" value="single" />						
												
						<div class="col-xs-12">					
					  	<div class="form-group">
							<label for="jform_<?= $item->field_name; ?>"><?= JText::_('COM_FORMULARIOS_SELECT_OPTION'); ?>: *</label>
							<select name="formId" class="form-control" required="true" id="jform_formId" data-msg="This field is required">
							<option value=""><?= JText::_('COM_FORMULARIOS_SELECT_OPTION'); ?></option>
							<?php foreach($model->getItem() as $item) : ?>							
							<option value="<?= $item->id; ?>"><?= JText::_($item->name); ?></option>
							<?php endforeach; ?>
							</select>
							
							<div class="validation"></div>
					  	</div>
					  	</div>
					  	
					  	<input type="hidden" name="Itemid" value="<?= $Itemid; ?>" />

					  	<div class="col-xs-12">
						  	<button type="submit" class="line-btn green submit"><?= JText::_('JSUBMIT'); ?></button>
					  	</div>
					</form>

				</div>
			</div>
			<!-- ./span12 -->
		</div>
		
	</div>
</section>
