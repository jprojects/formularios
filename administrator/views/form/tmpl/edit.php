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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_formularios/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'form.cancel') {
			Joomla.submitform(task, document.getElementById('form-form'));
		}
		else {
			
			if (task != 'form.cancel' && document.formvalidator.isValid(document.id('form-form'))) {
				
				Joomla.submitform(task, document.getElementById('form-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_formularios&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="form-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_FORMULARIOS_TITLE_FORM', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('heading'); ?>
				<?php echo $this->form->renderField('subheading'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php echo $this->form->renderField('advanced'); ?>
				<?php echo $this->form->renderField('child'); ?>
				<?php echo $this->form->renderField('registered'); ?>
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('redirect'); ?>
				<?php echo $this->form->renderField('success_msg'); ?>
				<?php echo $this->form->renderField('error_msg'); ?>
				<input type="hidden" name="jform[checked_out]" value="<?php echo JFactory::getUser()->id; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo date('Y-m-d H:i:s'); ?>" />

				<?php echo $this->form->renderField('created_by'); ?>

				<?php if ($this->state->params->get('save_history', 1)) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
				</div>
				<?php endif; ?>				
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
