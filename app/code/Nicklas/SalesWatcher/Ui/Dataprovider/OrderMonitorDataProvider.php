<?php

namespace Nicklas\SalesWatcher\Ui\DataProvider;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;

class OrderMonitorDataProvider
{
    protected $collection;
    protected $jsonResultFactory;

    public function __construct(
        \Nicklas\SalesWatcher\Model\ResourceModel\Order\Collection $collection,
        JsonFactory $jsonResultFactory
    ) {
        $this->collection = $collection;
        $this->jsonResultFactory = $jsonResultFactory;
    }
    
    /**
     * Retrieve data for the UI component as JSON
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function getDataAsJson()
    {
        $this->collection->clear(); // Ensure no filters are blocking data
        $data = $this->collection->toArray();

        $response = [
            'totalRecords' => $this->collection->getSize(),
            'items' => $data['items'],
        ];

        $jsonResult = $this->jsonResultFactory->create();
        return $jsonResult->setData($response);
    }
}
