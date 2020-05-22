<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Formularios
 * @author     aficat <kim@aficat.com>
 * @copyright  2018 aficat
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Formularios records.
 *
 * @since  1.6
 */
class FormulariosModelMessages extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'data_missatge', 'a.`data_missatge`',
				'name', 'f.`name`',
				'message', 'a.`message`',
				'state', 'a.`state`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = 'a.id', $direction = 'ASC')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$formid = $app->getUserStateFromRequest($this->context . '.filter.formId', 'filter_formId');
		$this->setState('filter.formId', $formid);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$comercial = $app->getUserStateFromRequest($this->context . '.filter.comercial', 'filter_comercial', '', 'string');
		$this->setState('filter.comercial', $comercial);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_formularios');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.formId');
		$id .= ':' . $this->getState('filter.comercial');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'a.*, f.name '
			)
		);
		$query->from('`#__formularios_stored` AS a');

		$query->join('INNER', '#__formularios_forms AS f ON f.id = a.formId');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('f.name LIKE ' . $search. ' OR a.message LIKE '. $search);
		}
		
		// Filter by search in title
		$formid = $this->getState('filter.formId');

		if (!empty($formid))
		{
			$query->where('formId = ' . $formid);
		}

		// Filter by state
		$state = $this->getState('filter.state');

		if (!empty($state))
		{
			$query->where('state = ' . $state);
		}

		// Filter by info comercial
		$comercial = $this->getState('filter.comercial');

		if (!empty($comercial))
		{
			$query->where('comercial = ' . $comercial);
		}
		
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		//echo $query;
		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();


		return $items;
	}
}
