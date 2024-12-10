<?php

namespace Nicklas\SalesWatcher\Model\ResourceModel\Order;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection as SalesOrderCollection;

class Collection extends SalesOrderCollection implements SearchResultInterface
{
    protected $aggregations;

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToSelect(['entity_id', 'increment_id', 'status', 'created_at']);
    }
    /**
     * Set Aggregations
     *
     * @param \Magento\Framework\Search\AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get Aggregations
     *
     * @return \Magento\Framework\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Get Search Criteria
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set Search Criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get Total Count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set Total Count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Get Items
     *
     * @return array
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * Set Items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
