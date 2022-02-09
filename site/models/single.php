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

class FormulariosModelSingle extends JModelItem
{ 
	/**
	 * Get a list of fields.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getItem($pk = null)
	{
        $app 	= JFactory::getApplication();
        $db  	= JFactory::getDbo();
        
        $id  	= $app->input->get('formId', 1);
		$step  	= $app->input->get('step', 0);
        
        $db->setQuery('SELECT * FROM `#__formularios_fields` WHERE state = 1 AND formId = '.($step != 0 ? $step : $id).' ORDER BY ordering');
        return $db->loadObjectList();
	}
	
	/**
	 * Get all
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getFormData($id)
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery('select * FROM `#__formularios_forms` WHERE id = '.$id);
        return $db->loadObject();
	}

	/**
	 * Get the number of forms in advanced mode
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getNumberOfForms($id)
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery('select COUNT(id) FROM `#__formularios_forms` WHERE child = '.$id);
        return $db->loadResult();
	}
}
