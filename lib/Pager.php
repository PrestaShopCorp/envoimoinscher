<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    EnvoiMoinsCher <informationapi@boxtale.com>
 * @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

class Pager {

	/**
	 * Private variable with pager options.
	 * @var array
	 */
	private $options = array();

	/**
	 * Class constructor. It initializes the options.
	 * @access public
	 * @param array $options Options list.
	 * @return void
	 */
	public function __construct($options = array())
	{
		$this->options = $options;
	}

	/**
	 * Prepares the Pager for a view template. 
	 * @access public
	 * @return array List of parameters which may be used in the view part.
	 */
	public function setPages()
	{
		return array(
			'url' => $this->getUrl(),
			'tag' => $this->getTag(),
			'last' => $this->setLastPage(),
			'after' => $this->getAfter(),
			'before' => $this->getBefore(),
			'actual' => $this->options['page'],
			'next' => $this->setNext(),
			'previous' => $this->setPrevious()
		);
	}

	/**
	 * Gets tag used in url.
	 * @access private
	 * @return string of tag.
	 */
	private function getTag()
	{
		if (isset($this->options['tag']))
		{
			return $this->options['tag'];
		}
		else
		{
			return 'p';
		}
	}

	/**
	 * Gets page's url.
	 * @access private
	 * @return string of url.
	 */
	private function getUrl()
	{
		if (isset($this->options['url']))
		{
			return $this->options['url'];
		}
		else
		{
			return '?pager';
		}
	}
	
	/**
	 * Gets number of pages before the actual page.
	 * @access private
	 * @return array List of pages.
	 */
	private function getBefore()
	{
		if ($this->options['page'] > 1)
		{
			$result = array();
			$limit = $this->options['page'] - $this->options['before'] - 1;
			$page = $this->options['page'] - 1;
			while ($page > 0 && $page > $limit)
			{
				$result[$page] = $page;
				$page--;
			}
			return array_reverse($result);
		}
		else
			return array();
	}

	/**
	 * Gets number of pages after the actual page.
	 * @access public
	 * @return array List of pages.
	 */
	private function getAfter()
	{
		if ($this->options['page'] < $this->last)
		{
			$limit = $this->options['page'] + $this->options['after'] + 1;
			$page = $this->options['page'] + 1;
			$result = array();
			while (($page < $limit && ($page <= $this->last)))
			{
				$result[$page] = $page;
				$page++;
			}
			return $result;
		}
		else
			return array();
	}

	/**
	 * Sets the next page number. May be used for the anchors like "next", "next page" etc.
	 * @access private
	 * @return int Returns integer when the next page exists.
	 */
	private function setNext()
	{
		if ($this->options['page'] < $this->last - 1)
			return $this->options['page'] + 1;
	}

	/**
	 * Sets the previous page number. May be used for the anchors like "previous", "previous page" etc.
	 * @access private
	 * @return int Returns integer when the previous page exists.
	 */

	private function setPrevious()
	{
		if ($this->options['page'] > 1)
			return $this->options['page'] - 1;
	}

	/**
	 * Sets the last page number. May be used for the anchors like "end", "the last page" etc.
	 * @access private
	 * @return int Returns integer with the laste page number.
	 */

	private function setLastPage()
	{
		$this->last = ceil($this->options['all'] / $this->options['perPage']);
		if ($this->last == 0)
			$this->last = 1;
		return $this->last;
	}
}