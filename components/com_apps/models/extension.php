<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This models supports retrieving lists of contact categories.
 *
 * @since  1.0.0
 */
class AppsModelExtension extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var	string
	 */
	public $_context = 'com_apps.extension';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var	string
	 */
	protected $_extension = 'com_apps';

	private $_parent = null;

	private $_catid = null;

	private $_items = null;

	private $_remotedb = null;

	public $_extensionstitle = null;

	private $_categories = null;

	private $_breadcrumbs = array();

	/**
	 * Method to auto-populate the model state.
	 *
	 * @note   Calling getState in this method will result in recursion.
	 *
	 * @since  1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = $app->input->getInt('id');
		$this->setState('filter.parentId', $parentId);

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('filter.published', 1);
		$this->setState('filter.access', true);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id	A prefix for the store id.
	 *
	 * @return  string  A store id
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.extension');
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return  mixed  An array of data items on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		if (!count($this->_items))
		{
			$menu   = JFactory::getApplication()->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;

			if ($active)
			{
				$params->loadString($active->params);
			}

			$options               = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories            = JCategories::getInstance('Contact', $options);
			$this->_parent         = $categories->get($this->getState('filter.parentId', 'root'));

			if (is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			}
			else
			{
				$this->_items = false;
			}
		}

		return $this->_items;
	}

	public function getParent()
	{
		if (!is_object($this->_parent))
		{
			$this->getItems();
		}

		return $this->_parent;
	}

	private function getBaseModel()
	{
		return JModelLegacy::getInstance('Base', 'AppsModel');
	}

	private function getCatID()
	{
		return $this->_catid;
	}

	public function getCategories()
	{
		return $this->getBaseModel()->getCategories($this->getCatID());
	}

	public function getBreadcrumbs()
	{
		return $this->getBaseModel()->getBreadcrumbs($this->getCatID());
	}

	public function getPluginUpToDate()
	{
		return $this->getBaseModel()->getPluginUpToDate();
	}

	public function getExtension()
	{
		// Get extension id
		$http    = new JHttp;
		$api_url = new JUri;
		$input   = new JInput;
		$id      = $input->get('id', null, 'int');
		$release = preg_replace('/[^\d]/', '', base64_decode($input->get('release', '', 'base64')));
		$release = intval($release / 5) * 5;
		$cache   = JFactory::getCache();

		$cache->setCaching(1);

		$api_url->setScheme('http');
		$api_url->setHost('extensions.joomla.org/index.php');
		$api_url->setvar('option', 'com_jed');
		$api_url->setvar('controller', 'filter');
		$api_url->setvar('view', 'extension');
		$api_url->setvar('format', 'json');
		$api_url->setvar('filter[approved]', '1');
		$api_url->setvar('filter[published]', '1');
		$api_url->setvar('extend', '0');
		$api_url->setvar('filter[id]', $id);

		$extension_json = $cache->call(array($http, 'get'), $api_url);

		// Create item
		$items             = json_decode($extension_json->body);
		$item              = $items->data[0];
		$this->_catid      = $item->core_catid->value;
		$item->image       = $this->getBaseModel()->getMainImageUrl($item);
		$item->downloadurl = $item->download_integration_url->value;

		if (preg_match('/\.xml\s*$/', $item->downloadurl))
		{
			$app       = JFactory::getApplication();
			$product   = addslashes(base64_decode($app->input->get('product', '', 'base64')));
			$release   = preg_replace('/[^\d\.]/', '', base64_decode($app->input->get('release', '', 'base64')));
			$dev_level = (int) base64_decode($app->input->get('dev_level', '', 'base64'));

			$updatefile = JPATH_ROOT . '/libraries/joomla/updater/update.php';
			$fh         = fopen($updatefile, 'r');
			$theData    = fread($fh, filesize($updatefile));
			fclose($fh);

			$theData = str_replace('<?php', '', $theData);
			$theData = str_replace('$ver->PRODUCT', "'".$product."'", $theData);
			$theData = str_replace('$ver->RELEASE', "'".$release."'", $theData);
			$theData = str_replace('$ver->DEV_LEVEL', "'".$dev_level."'", $theData);

			eval($theData);

			$update = new JUpdate;
			$update->loadFromXML($item->downloadurl);
			$package_url_node = $update->get('downloadurl', false);

			if (isset($package_url_node->_data))
			{
				$item->downloadurl = $package_url_node->_data;
 			}
		}

		$item->download_type = $this->getTypeEnum($item->download_integration_type->value);

		return array($item);
	}

	private function getTypeEnum($text)
	{
		$options    = array();
		$options[0] = 'None';
		$options[1] = 'Free Direct Download link:';
		$options[2] = 'Free but Registration required at link:';
		$options[3] = 'Commercial purchase required at link:';

		return array_search($text, $options);
	}

}
