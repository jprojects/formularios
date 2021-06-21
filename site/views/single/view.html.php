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

class FormulariosViewSingle extends JViewLegacy
{
    protected $item;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->item 	= $this->get('Item');
        $this->params 	= $app->getParams('com_formularios');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode("\n", $errors));
			return false;
		}
		
		$active  = $app->getMenu()->getActive();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$menu    = $menus->getActive();
		
		$title = $this->params->get('page_title', '');

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }       

        $this->_prepareDocument();

        parent::display($tpl);
    }
        
    /**
	 * Method to set up the document properties
	 *
	 * @return void
	*/
	protected function _prepareDocument()
	{
		$app        = JFactory::getApplication();		
        $document   = JFactory::getDocument();
		$menus      = $app->getMenu();
		$title      = null;
		
		$params 	= JComponentHelper::getParams( 'com_formularios' );
		$captcha    = $params->get('reCaptcha', 0);
		$sitekey    = $params->get('reCaptcha_sitekey');

		$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js');
		
		if($captcha == 1) {
			$document->addScript('https://www.google.com/recaptcha/api.js?render='.$sitekey);
		}

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$title = $menu->title;
		} else {
			$title = JText::_('COM_BOTIGA_DEFAULT_PAGE_TITLE');
		}

		$this->document->setTitle($title);
	}
}
?>
