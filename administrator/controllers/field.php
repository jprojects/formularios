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

jimport('joomla.application.component.controllerform');

/**
 * Form controller class.
 *
 * @since  1.6
 */
class FormulariosControllerField extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'fields';
		parent::__construct();
	}
	
	/**
	* Method to override the save method
	 *
	 * @return	void
	*/
    function save()
	{
		$app    = JFactory::getApplication();
		$data 	= $app->input->post->get('jform', array(), 'array');

		parent::save();
		
		$this->setRedirect('index.php?option=com_formularios&view=fields&formId='.$data['formId']);
	}
}
