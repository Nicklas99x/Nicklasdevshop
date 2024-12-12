<?php

namespace Nicklas\SalesWatcher\Ui\DataProvider;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class SalesOverviewDataProvider
{
    protected $orderCollectionFactory;

    public function __construct(
        CollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Get total sales and average order value for a specified date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getSalesData($startDate, $endDate)
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('created_at', ['gteq' => $startDate])
                   ->addFieldToFilter('created_at', ['lteq' => $endDate])
                   ->addFieldToFilter('status', ['eq' => 'complete']); // Filter for 'complete' status

        $totalSales = 0;
        $totalOrders = 0;

        foreach ($collection as $order) {
            $totalSales += $order->getGrandTotal();
            $totalOrders++;
        }

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return [
            'total_sales' => $totalSales,
            'average_order_value' => $averageOrderValue,
            'total_orders' => $totalOrders,
        ];
    }
}
