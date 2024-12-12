<?php

namespace Nicklas\SalesWatcher\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Nicklas\SalesWatcher\Ui\DataProvider\SalesOverviewDataProvider;

class SalesOverview extends Action
{
    protected $jsonResultFactory;
    protected $dataProvider;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        SalesOverviewDataProvider $dataProvider
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->dataProvider = $dataProvider;
    }

    public function execute()
    {
        $timeRanges = [
            'last_7_days' => [
                'start' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'end' => date('Y-m-d H:i:s'),
            ],
            'last_month' => [
                'start' => date('Y-m-01 00:00:00', strtotime('first day of this month -1 month')),
                'end' => date('Y-m-d H:i:s'), // Include up to now
            ],
            'last_quarter' => [
                'start' => date('Y-m-d H:i:s', strtotime('first day of this month -3 months')),
                'end' => date('Y-m-d H:i:s'), // Include up to now
            ],
            'last_year' => [
                'start' => date('Y-01-01 00:00:00', strtotime('-1 year')),
                'end' => date('Y-m-d H:i:s'), // Include up to now
            ],
            'all_time' => [
                'start' => '1970-01-01 00:00:00',
                'end' => date('Y-m-d H:i:s'),
            ],
        ];

        $timeRange = $this->getRequest()->getParam('range', 'all_time');

        if (!isset($timeRanges[$timeRange])) {
            $timeRange = 'all_time'; // Default to all-time if an invalid range is provided
        }

        $salesData = $this->dataProvider->getSalesData($timeRanges[$timeRange]['start'], $timeRanges[$timeRange]['end']);

        $result = $this->jsonResultFactory->create();
        return $result->setData($salesData);
    }
}
