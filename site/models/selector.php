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

class FormulariosModelSelector extends JModelItem
{ 
	/**
	 * Get a list of forms for selector.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getItem()
	{
        $db  = JFactory::getDbo();
        
        $db->setQuery('SELECT id, name FROM #__formularios_forms WHERE state = 1 AND selector = 1');
        return $db->loadObjectList();
	}

}
