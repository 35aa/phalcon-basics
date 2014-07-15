<?php
//TODO: add comment about first paginator's page always should be 1
namespace Framework\Paginator\Adapter;

class ModelTable implements \Phalcon\Paginator\AdapterInterface {

	const ITEMS_PER_PAGE = 5;

	protected $_stepNumber;

	protected $_dataProvider;

	protected $_itemsPerPage;

	/**
	 * This is __construct. Put here all needed initialization.
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		$this->_itemsPerPage = self::ITEMS_PER_PAGE;
		if (is_array($config)) {
			if (isset($config['dataProvider'])) {
				$this->setDataProvider($config['dataProvider']);
			}
			if (isset($config['pageNumber'])) {
				$this->setCurrentPage($config['pageNumber']);
			}
			if (isset($config['itemsPerPage'])) {
				$this->_itemsPerPage = $config['itemsPerPage'];
			}
		}
	}

	/**
	 * Set current page number
	 *
	 * @param int $page
	 */
	public function setCurrentPage($page) {
		$this->_stepNumber = intval($page) && intval($page) > 0 && intval($page) <= $this->getNumberOfPages() ? intval($page) : 1;
	}

	/**
	 * Get current page number
	 *
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->_stepNumber;
	}

	/**
	 * Get previous page number
	 *
	 * @return int
	 */
	public function getPreviousPage() {
		return $this->getCurrentPage() > 1 ? $this->getCurrentPage() - 1 : 0;
	}

	/**
	 * Get next page number
	 *
	 * @return int
	 */
	public function getNextPage() {
		return $this->getCurrentPage() < $this->getNumberOfPages() ? $this->getCurrentPage() + 1 : 0;
	}

	/**
	 * Set current page number
	 *
	 * @param \Framework\Paginator\Adapter\DataProviderInterface $page
	 */
	public function setDataProvider(DataProviderInterface $dataProvider) {
		$this->_dataProvider = $dataProvider;
	}

	/**
	 * Get current page number
	 *
	 * @return instance of interface \Framework\Paginator\Adapter\DataProviderInterface
	 */
	public function getDataProvider() {
		return $this->_dataProvider;
	}

	/**
	 * Get totla number of pages
	 *
	 * @returns int
	 */
	public function getNumberOfPages() {
		if ($this->getDataProvider()) {
			return floor($this->getDataProvider()->getCount()/$this->getItemsPerPage());
		}
		else {
			return 1;
		}
	}

	/**
	 * Get number of items perPage
	 *
	 * @returns int
	 */
	public function getItemsPerPage() {
		return self::ITEMS_PER_PAGE;
	}

	/**
	 * Returns data for paginating
	 *
	 * @return stdClass
	 */
	public function getPaginate() {
		return $this->getDataProvider()->getItemsWithOffsetAndLimit(($this->getCurrentPage() - 1) * $this->getItemsPerPage() + 1, $this->getItemsPerPage());
	}

}

