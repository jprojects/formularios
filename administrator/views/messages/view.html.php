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

jimport('joomla.application.component.view');

/**
 * View class for a list of Formularios.
 *
 * @since  1.6
 */
class FormulariosViewMessages extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		FormulariosHelper::addSubmenu('messages');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = FormulariosHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FORMULARIOS_TITLE_FORMS'), 'forms.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/messages';

		JToolBarHelper::back();
		JToolBarHelper::divider();
		
		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('message.edit', 'JTOOLBAR_EDIT');
			}
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('messages.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('messages.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'messages.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolbarHelper::trash('messages.trash');
			}
		}
	

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_formularios');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_formularios&view=messages');
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{

	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
