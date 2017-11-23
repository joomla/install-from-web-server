<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Exception\CacheExceptionInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;

JLoader::register('AppsModelBase', __DIR__ . '/base.php');

/**
 * Extension model.
 *
 * @since  1.0
 */
class AppsModelExtension extends AppsModelBase
{
	/**
	 * The active category ID
	 *
	 * @var    integer|null
	 * @since  1.0
	 */
	private $catid;

	/**
	 * Fetches the extension data from the JED
	 *
	 * @param   integer  $id  The extension ID
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  RuntimeException if the HTTP query fails
	 */
	public function fetchExtension($id)
	{
		$url = new Uri;

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
			$http = $this->getHttpClient();
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

	/**
	 * Fetch the breadcrumb tree
	 *
	 * @param   integer|null  $catid  The active category ID
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getBreadcrumbs($catid = null): array
	{
		return parent::getBreadcrumbs($this->getCatID());
	}

	/**
	 * Get the active category ID
	 *
	 * @return  integer|null
	 *
	 * @since   1.0
	 */
	private function getCatID()
	{
		return $this->catid;
	}

	/**
	 * Fetch the JED categories
	 *
	 * @param   integer|null  $catid  The active category ID
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getCategories($catid = null): array
	{
		return parent::getCategories($this->getCatID());
	}

	/**
	 * Fetch the extension data.
	 *
	 * @return  stdClass
	 *
	 * @since   1.0
	 */
	public function getExtension()
	{
		/** @var \Joomla\CMS\Cache\Controller\CallbackController $cache */
		$cache = Factory::getCache('com_apps', 'callback');

		// These calls are always cached
		$cache->setCaching(true);

		$id = $this->getState('filter.id');

		try
		{
			// We explicitly define our own ID to keep the cache API from calculating it separately
			$items = $cache->get([$this, 'fetchExtension'], [$id], md5(__METHOD__ . $id));
		}
		catch (CacheExceptionInterface $e)
		{
			// Cache failure, let's try an HTTP request without caching
			$items = $this->fetchExtension($id);
		}
		catch (RuntimeException $e)
		{
			// Other failure, this isn't good
			Log::add(
				'Could not retrieve extension data from the JED: ' . $e->getMessage(),
				Log::ERROR,
				'com_apps'
			);

			// Throw a "sanitised" Exception but nest the caught Exception for debugging
			throw new RuntimeException('Could not retrieve extension data from the JED.', $e->getCode(), $e);
		}

		// Create item
		$item              = $items->data[0];
		$this->catid       = $item->core_catid->value;
		$item->image       = $this->getMainImageUrl($item);
		$item->downloadurl = $item->download_integration_url->value;

		if (preg_match('/\.xml\s*$/', $item->downloadurl))
		{
			$product   = addslashes(base64_decode($this->getState('filter.product')));
			$dev_level = (int) base64_decode($this->getState('filter.dev_level'));

			$updatefile = dirname(__DIR__) . '/helpers/update.php';
			$fh         = fopen($updatefile, 'r');
			$theData    = fread($fh, filesize($updatefile));
			fclose($fh);

			$theData = str_replace('<?php', '', $theData);
			$theData = str_replace('JVersion::PRODUCT', "'" . $product . "'", $theData);
			$theData = str_replace('JVersion::DEV_LEVEL', "'" . $dev_level . "'", $theData);

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

		return $item;
	}

	/**
	 * Get the type value
	 *
	 * @param   string  $text  Text to lookup
	 *
	 * @return  false|int|string
	 *
	 * @since   1.0
	 */
	private function getTypeEnum($text)
	{
		$options    = [];
		$options[0] = 'None';
		$options[1] = 'Free Direct Download link:';
		$options[2] = 'Free but Registration required at link:';
		$options[3] = 'Commercial purchase required at link:';

		return array_search($text, $options);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$app = Factory::getApplication();

		$this->setState('filter.id', $app->input->getUint('id'));
		$this->setState('filter.product', $app->input->getBase64('product', ''));
		$this->setState('filter.release', $app->input->getBase64('release', ''));
		$this->setState('filter.dev_level', $app->input->getBase64('dev_level', ''));
	}
}
