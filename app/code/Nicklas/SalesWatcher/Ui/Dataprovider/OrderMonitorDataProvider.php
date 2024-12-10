<?php

namespace Nicklas\SalesWatcher\Ui\DataProvider;

use Magento\Framework\Controller\Result\JsonFactory;

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
     * @param array $statuses
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function getDataAsJson(array $statuses = [])
    {
        $this->collection->clear(); // Clear any existing filters

        // Apply filtering by order statuses
        if (!empty($statuses)) {
            $this->collection->addFieldToFilter('status', ['in' => $statuses]);
        }

        // Convert collection to array
        $data = $this->collection->toArray();

        $response = [
            'totalRecords' => $this->collection->getSize(),
            'items' => $data['items'],
        ];

        // Create and return JSON response
        $jsonResult = $this->jsonResultFactory->create();
        return $jsonResult->setData($response);
    }
}
