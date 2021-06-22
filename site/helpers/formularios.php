<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Formularios
 * @author     aficat <kim@aficat.com>
 * @copyright  2018 aficat
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class FormulariosFrontendHelper
 *
 * @since  1.6
 */
class FormulariosHelpersFormularios
{
	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}
	
	/**
	 * Method to get the privacy or terms article by language
     * @access public
     * @return the article url
    */
    public static function getPrivacyPolicy()
    {
    	$params = JComponentHelper::getParams( 'com_formularios' );
		$lang = JFactory::getLanguage()->getTag();
		$articles = json_decode($params->get('privacy'));
		foreach ($articles as $art) 
      	{
			foreach ($art as $k => $v) 
			{
				$result[$k][] = $v;
			}
      	}
      	
	  	foreach ($result as $index=>$value) 
		{   
			if($value[0] == $lang) { return $value[1]; }
		}
    }
    
    /**
	 * Method to get the footer legal notice
     * @access public
     * @return the article url
    */
    public static function getFooter()
    {
    	$params = JComponentHelper::getParams( 'com_formularios' );
		$lang = JFactory::getLanguage()->getTag();
		$articles = json_decode($params->get('footer'));
		foreach ($articles as $art) 
      	{
			foreach ($art as $k => $v) 
			{
				$result[$k][] = $v;
			}
      	}
      	
	  	foreach ($result as $index=>$value) 
		{   
			if($value[0] == $lang) { return $value[1]; }
		}
    }

	/**
	 * method to write in a debug log
	 * @param $text string The text to write in the log file
	 * @param $logfile string The file where to write the log file
	 * @params $logfile the destination lof file
	*/
    public static function customLog($text, $logfile=null) {
		if ($logfile==null) {
			$logfile = JPATH_COMPONENT.'/logs/afi.log';
		}
		$handle = fopen($logfile, 'a');
		fwrite($handle, date('d-M-Y H:i:s') . ': ' . $text . "\n");
		fclose($handle);
	}

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_formularios'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_formularios') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
}
