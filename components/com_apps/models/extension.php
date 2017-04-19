<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package     Joomla.Site
 * @subpackage  com_apps
 * @since       1.6
 */
class AppsModelExtension extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_apps.extension';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
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
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
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

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
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
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if (!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
			if ($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories = JCategories::getInstance('Contact', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if (is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			} else {
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

	/**
	 * @return  bool|AppsModelBase
	 */
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
		$base_model = $this->getBaseModel();
		return $base_model->getCategories($this->getCatID());
	}

	public function getBreadcrumbs()
	{
		$base_model = $this->getBaseModel();
		return $base_model->getBreadcrumbs($this->getCatID());
	}

	public function getPluginUpToDate()
	{
		$base_model = $this->getBaseModel();
		return $base_model->getPluginUpToDate();
	}

	public function getExtension()
	{
		/** @var JCacheControllerCallback $cache */
		$cache = JFactory::getCache('com_apps', 'callback');

		// These calls are always cached
		$cache->setCaching(true);

		// Extract params from the request
		$input = JFactory::getApplication()->input;

		$id = $input->getInt('id', 0);

		try
		{
			// We explicitly define our own ID to keep JCache from calculating it separately
			$items = $cache->get(array($this, 'fetchExtension'), array($id), md5(__METHOD__ . $id));
		}
		catch (JCacheException $e)
		{
			// Cache failure, let's try an HTTP request without caching
			$items = $this->fetchExtension($id);
		}
		catch (RuntimeException $e)
		{
			// Other failure, this isn't good
			JLog::add(
				'Could not retrieve extension data from the JED: ' . $e->getMessage(),
				JLog::ERROR,
				'com_apps'
			);

			// Throw a "sanitised" Exception but nest the caught Exception for debugging
			throw new RuntimeException('Could not retrieve extension data from the JED.', $e->getCode(), $e);
		}

		// Create item
		$item              = $items->data[0];
		$this->_catid      = $item->core_catid->value;
		$item->image       = $this->getBaseModel()->getMainImageUrl($item);
		$item->downloadurl = $item->download_integration_url->value;

		if (preg_match('/\.xml\s*$/', $item->downloadurl))
		{
			$product   = addslashes(base64_decode($input->getBase64('product', '')));
			$release   = preg_replace('/[^\d\.]/', '', base64_decode($input->getBase64('release', '')));
			$dev_level = (int) base64_decode($input->getBase64('dev_level', ''));

			$updatefile = JPATH_ROOT . '/libraries/joomla/updater/update.php';
			$fh         = fopen($updatefile, 'r');
			$theData    = fread($fh, filesize($updatefile));
			fclose($fh);

			$theData = str_replace('<?php', '', $theData);
			$theData = str_replace('$ver->PRODUCT', "'" . $product . "'", $theData);
			$theData = str_replace('$ver->RELEASE', "'" . $release . "'", $theData);
			$theData = str_replace('$ver->DEV_LEVEL', "'" . $dev_level . "'", $theData);

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

	/**
	 * Fetches the extension data from the JED
	 *
	 * @param   integer  $id  The extension ID
	 *
	 * @return  array
	 *
	 * @throws  RuntimeException if the HTTP query fails
	 */
	public function fetchExtension($id)
	{
		$url = new JUri;

		$url->setScheme('https');
		$url->setHost('extensions.joomla.org');
		$url->setPath('/index.php');
		$url->setVar('option', 'com_jed');
		$url->setVar('controller', 'filter');
		$url->setVar('view', 'extension');
		$url->setVar('format', 'json');
		$url->setVar('filter[approved]', '1');
		$url->setVar('filter[published]', '1');
		$url->setVar('filter[id]', $id);
		$url->setVar('extend', '0');

		try
		{
			$http = $this->getBaseModel()->getHttpClient();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException('Cannot fetch HTTP client to connect to JED', $e->getCode(), $e);
		}

		$response = $http->get($url->toString());

		// Make sure we've gotten an expected good response
		if ($response->code !== 200)
		{
			throw new RuntimeException('Unexpected response from the JED', $response->code);
		}

		// The body should be a JSON string, if we have issues decoding it assume we have a bad response
		$categoryData = json_decode($response->body);

		if (json_last_error())
		{
			throw new RuntimeException('Unexpected response from the JED, JSON could not be decoded with error: ' . json_last_error_msg(), 500);
		}

		return $categoryData;
	}

	private function getTypeEnum($text) {
		$options = array();
		$options[0] = 'None';
		$options[1] = 'Free Direct Download link:';
		$options[2] = 'Free but Registration required at link:';
		$options[3] = 'Commercial purchase required at link:';

		return array_search($text, $options);
	}

}
