<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Formularios
 * @author     aficat <kim@aficat.com>
 * @copyright  2018 aficat
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

define('DS', DIRECTORY_SEPARATOR);

JLoader::registerPrefix('Formularios', JPATH_COMPONENT);

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'formularios.php');

JFactory::getDocument()->addStylesheet('components/com_formularios/assets/css/forms.css');

// Execute the task.
$controller = JControllerLegacy::getInstance('Formularios');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
