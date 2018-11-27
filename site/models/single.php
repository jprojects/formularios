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
	public function getItem()
	{
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        
        $id  = $app->input->get('formId', 1);
        
        $db->setQuery('SELECT * FROM #__formularios_fields WHERE state = 1 AND formId = '.$id);
        return $db->loadObjectList();
	}
	/**
	 * Get a form title.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getFormData($field, $formid=1)
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery("select $field FROM #__formularios_forms where id = ".$formid);
        return $db->loadResult();
	}
	/**
	 * Get a form title.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getFormTitle($formid=1)
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery('select name FROM #__formularios_forms where id = '.$formid);
        return $db->loadResult();
	}
	/**
	 * Know if it's a login form
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function isLogin($id)
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery('select registered FROM #__formularios_forms WHERE id = '.$id);
        if($db->loadResult() == 1) {
        	return true;
        }
        return false;
	}
}
