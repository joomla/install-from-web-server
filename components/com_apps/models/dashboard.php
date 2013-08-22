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
class AppsModelDashboard extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_apps.dashboard';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_apps';

	private $_parent = null;

	private $_items = null;

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
	
	public function getCategories() {
		
		
		$categories[0] = new stdclass;
		$categories[0]->id = 1;
		$categories[0]->name = 'Access & Security';
		$children[0] = new stdclass;
		$children[0]->id = 2;
		$children[0]->name = 'Site Access';
		$children[1] = new stdclass;
		$children[1]->id = 3;
		$children[1]->name = 'Site Security';
		$categories[0]->children = $children;
		
		$categories[1] = new stdclass;
		$categories[1]->id = 4;
		$categories[1]->name = 'Administration';
		$children[0]->id = 5;
		$children[0]->name = 'Admin Desk';
		$children[1]->id = 6;
		$children[1]->name = 'Admin Navigation';
		$children[1]->id = 7;
		$children[1]->name = 'Admin Performance';
		$children[1]->id = 8;
		$children[1]->name = 'Admin Reports';
		$children[1]->id = 9;
		$children[1]->name = 'Admin Templates';
		$children[1]->id = 10;
		$children[1]->name = 'Extensions Quick Icons';
		$categories[1]->children = $children;
		
		return $categories;
	}

	public function getExtensions() {
		$extension[0] = new stdclass;
		$extension[0]->id = 1;
		$extension[0]->name = 'JBolo!';
		$extension[0]->user = 'techjoomla';
		$extension[0]->rating = 4.5;
		$extension[0]->tags = array('C','P','M','S');
		$extension[0]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/51430.png';
		$extension[0]->compatibility = array('2.5', '3.0');
		$extension[0]->version = 3.8;
		$extension[0]->downloadurl = '';
		
		$extension[1] = new stdclass;
		$extension[1]->id = 2;
		$extension[1]->name = 'Akeeba Backup';
		$extension[1]->user = 'nikosdion';
		$extension[1]->rating = 4.5;
		$extension[1]->tags = array('C','P','M','S');
		$extension[1]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/13048.png';
		$extension[1]->compatibility = array('2.5', '3.0');
		$extension[1]->version = '3.7.10';
		$extension[1]->downloadurl = 'https://www.akeebabackup.com/download/akeeba-backup/3-7-10/com_akeeba-3-7-10-core-zip.raw';
		
		$extension[2] = new stdclass;
		$extension[2]->id = 3;
		$extension[2]->name = 'Advanced Module Manager';
		$extension[2]->user = 'nikosdion';
		$extension[2]->rating = 5;
		$extension[2]->tags = array('C','P','M','S');
		$extension[2]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/48195.png';
		$extension[2]->compatibility = array('2.5', '3.0');
		$extension[2]->version = '4.7.1';
		$extension[2]->downloadurl = 'http://download.nonumber.nl/?ext=advancedmodulemanager';
		
		$extension[3] = new stdclass;
		$extension[3]->id = 2;
		$extension[3]->name = 'Admin Tools';
		$extension[3]->user = 'nikosdion';
		$extension[3]->rating = 4.5;
		$extension[3]->tags = array('C','P','M','S');
		$extension[3]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/19229.png';
		$extension[3]->compatibility = array('2.5', '3.0');
		$extension[3]->version = '2.5.6';
		$extension[3]->downloadurl = 'https://www.akeebabackup.com/download/admintools/2-5-6/comadmintools-2-5-6-core-zip.raw';
		
		$extension[4] = new stdclass;
		$extension[4]->id = 2;
		$extension[4]->name = 'Akeeba Backup';
		$extension[4]->user = 'nikosdion';
		$extension[4]->rating = 4.5;
		$extension[4]->tags = array('C','P','M','S');
		$extension[4]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/13048.png';
		$extension[4]->compatibility = array('2.5', '3.0');
		$extension[4]->version = array('2.5', '3.0');
		$extension[4]->downloadurl = '';

		$extension[5] = new stdclass;
		$extension[5]->id = 3;
		$extension[5]->name = 'Advanced Module Manager';
		$extension[5]->user = 'nikosdion';
		$extension[5]->rating = 5;
		$extension[5]->tags = array('C','P','M','S');
		$extension[5]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/48195.png';
		$extension[5]->compatibility = array('2.5', '3.0');
		$extension[5]->version = '4.7.1';
		$extension[5]->downloadurl = 'http://download.nonumber.nl/?ext=advancedmodulemanager';
		
		$extension[6] = new stdclass;
		$extension[6]->id = 2;
		$extension[6]->name = 'Admin Tools';
		$extension[6]->user = 'nikosdion';
		$extension[6]->rating = 4.5;
		$extension[6]->tags = array('C','P','M','S');
		$extension[6]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/19229.png';
		$extension[6]->compatibility = array('2.5', '3.0');
		$extension[6]->version = '2.5.6';
		$extension[6]->downloadurl = 'https://www.akeebabackup.com/download/admintools/2-5-6/comadmintools-2-5-6-core-zip.raw';
		
		$extension[7] = new stdclass;
		$extension[7]->id = 2;
		$extension[7]->name = 'Akeeba Backup';
		$extension[7]->user = 'nikosdion';
		$extension[7]->rating = 4.5;
		$extension[7]->tags = array('C','P','M','S');
		$extension[7]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/13048.png';
		$extension[7]->compatibility = array('2.5', '3.0');
		$extension[7]->version = array('2.5', '3.0');
		$extension[7]->downloadurl = '';
		
		$extension[8] = new stdclass;
		$extension[8]->id = 2;
		$extension[8]->name = 'Akeeba Backup';
		$extension[8]->user = 'nikosdion';
		$extension[8]->rating = 4.5;
		$extension[8]->tags = array('C','P','M','S');
		$extension[8]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/13048.png';
		$extension[8]->compatibility = array('2.5', '3.0');
		$extension[8]->version = array('2.5', '3.0');
		$extension[8]->downloadurl = '';

		$extension[9] = new stdclass;
		$extension[9]->id = 3;
		$extension[9]->name = 'Advanced Module Manager';
		$extension[9]->user = 'nikosdion';
		$extension[9]->rating = 5;
		$extension[9]->tags = array('C','P','M','S');
		$extension[9]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/48195.png';
		$extension[9]->compatibility = array('2.5', '3.0');
		$extension[9]->version = '4.7.1';
		$extension[9]->downloadurl = 'http://download.nonumber.nl/?ext=advancedmodulemanager';
		
		$extension[10] = new stdclass;
		$extension[10]->id = 2;
		$extension[10]->name = 'Admin Tools';
		$extension[10]->user = 'nikosdion';
		$extension[10]->rating = 4.5;
		$extension[10]->tags = array('C','P','M','S');
		$extension[10]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/19229.png';
		$extension[10]->compatibility = array('2.5', '3.0');
		$extension[10]->version = '2.5.6';
		$extension[10]->downloadurl = 'https://www.akeebabackup.com/download/admintools/2-5-6/comadmintools-2-5-6-core-zip.raw';
		
		$extension[11] = new stdclass;
		$extension[11]->id = 2;
		$extension[11]->name = 'Akeeba Backup';
		$extension[11]->user = 'nikosdion';
		$extension[11]->rating = 4.5;
		$extension[11]->tags = array('C','P','M','S');
		$extension[11]->image = 'http://extensions.joomla.org/components/com_mtree/img/listings/s/13048.png';
		$extension[11]->compatibility = array('2.5', '3.0');
		$extension[11]->version = array('2.5', '3.0');
		$extension[11]->downloadurl = '';
		
		return $extension;
		
	}

}
