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

	private function getRemoteDB() {
		$base_model = $this->getBaseModel();
		return $base_model->getRemoteDB();
	}
	
	private function getBaseModel()
	{
		$base_model = JModelLegacy::getInstance('Base', 'AppsModel');
		return $base_model;
	}
	
	private function getCatID()
	{
		$input = new JInput;
		$id = $input->get('id', null, 'int');

		// Get remote database
		$db = $this->getRemoteDB();
		
		// Get category id
		$query = $db->getQuery(true);
		$query->select('cat_id');
		$query->from('jos_mt_cl');
		$query->where('link_id = ' . (int)$id);
		$db->setQuery($query);
		return $db->loadResult();
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
	
	public function getExtension()
	{
		// Get extension id
		$input = new JInput;
		$id = $input->get('id', null, 'int');
		
		// Get remote database
		$db = $this->getRemoteDB();
		
		$query = 'SET SESSION group_concat_max_len=15000';
		$db->setQuery($query);
		$db->execute();
		
		// Form query
		$query = $db->getQuery(true);
		$query->select(
			array(
				't2.*',
				't3.filename AS image',
				't6.cat_name AS cat_name',
				't6.cat_id AS cat_id',
				'CONCAT("{", GROUP_CONCAT("\"", t5.caption, "\":\"", t4.value, "\""), "}") AS options',
			)
		);


		$query->from('jos_mt_links AS t2');
		$query->join('LEFT', 'jos_mt_cl AS t1 ON t1.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_images AS t3 ON t3.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_cfvalues AS t4 ON t2.link_id = t4.link_id');
		$query->join('LEFT', 'jos_mt_customfields AS t5 ON t4.cf_id = t5.cf_id');
		$query->join('LEFT', 'jos_mt_cats AS t6 ON t6.cat_id = t1.cat_id');

		$where = array(
			't2.link_id = ' . (int)$id,
			't2.link_published = 1',
			't2.link_approved = 1',
			'(t2.publish_up <= NOW() OR t2.publish_up = "0000-00-00 00:00:00")',
			'(t2.publish_down >= NOW() OR t2.publish_down = "0000-00-00 00:00:00")',
		);

		$query->where($where);
		$query->group('t2.link_id');
		$db->setQuery($query);
		$item = $db->loadObject();

		// Get CDN URL
		$componentParams = JComponentHelper::getParams('com_apps');
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";
		
		// Create item
		$options = new JRegistry($item->options);
		$item->image = $cdn . $item->image;
		$item->user = $options->get('Developer Name');
		$item->tags = explode('|', trim($options->get('Extension Includes')));
		$item->compatibility = $options->get('Compatibility');
		$item->version = $options->get('Version');
		$item->downloadurl = $options->get('Link for download/registration/purchase: URL');
		$item->type = $options->get('Extension Apps* download type');
		$item->license = $options->get('License');
		$item->type = $options->get('Extension Apps* download type');
		
		return array($item);
		
	}

}
