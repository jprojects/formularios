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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.multiselect');

$formid = JFactory::getApplication()->input->get('formId');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_formularios/assets/css/formularios.css');
$document->addStyleSheet(JUri::root() . 'media/com_formularios/css/list.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_formularios');
$saveOrder = $listOrder == 'a.`ordering`';

// if ($saveOrder && !empty($this->items))
// {
	$saveOrderingUrl = 'index.php?option=com_formularios&task=fields.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
//}

$sortFields = $this->getSortFields();
?>

<form action="<?php echo JRoute::_('index.php?option=com_formularios&view=fields&formId='.$formid); ?>" method="post"
	  name="adminForm" id="adminForm">

		<div id="j-main-container">

            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="formList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                        </th>
					<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<?php if (isset($this->items[0]->state)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
					</th>
					<?php endif; ?>

					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_FORMULARIOS_FORMS_ID', 'a.`id`', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_FORMULARIOS_FORMS_FIELDNAME', 'a.`field_name`', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_FORMULARIOS_FORMS_FIELDTYPE', 'a.`field_type`', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_FORMULARIOS_FORMS_FIELDREQUIRED', 'a.`field_required`', $listDirn, $listOrder); ?>
					</th>
					<th></th>
					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody  class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true">
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_formularios');
					$canEdit    = $user->authorise('core.edit', 'com_formularios');
					$canCheckin = $user->authorise('core.manage', 'com_formularios');
					$canChange  = $user->authorise('core.edit.state', 'com_formularios');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="text-center d-none d-md-table-cell">
									<?php
									$iconClass = '';
									
									?>
									<span class="sortable-handler<?php echo $iconClass; ?>">
										<span class="icon-ellipsis-v" aria-hidden="true"></span>
									</span>
									<?php //if ($canChange && $saveOrder) : ?>
										<input type="text" name="order[]" size="5"
											value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
									<?php //endif; ?>
								</td>
						<?php endif; ?>
						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if (isset($this->items[0]->state)): ?>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->state, $i, 'forms.', $canChange, 'cb'); ?>
						</td>
						<?php endif; ?>

										<td>

					<?php echo $item->id; ?>
				</td>				<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'forms.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_formularios&task=field.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->field_name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->field_name); ?>
				<?php endif; ?>
				</td>
				<td><?= $item->field_type; ?></td>
				<td><?= $item->field_required == 0 ? 'No' : 'Si'; ?></td>
				<td><a class="btn btn-default" href="index.php?option=com_formularios&task=fields.eliminar&id=<?= $item->id; ?>&formId=<?= $formid; ?>">Eliminar</a></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>
