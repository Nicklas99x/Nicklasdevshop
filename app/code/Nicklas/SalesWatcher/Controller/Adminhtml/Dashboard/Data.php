<?php

namespace Nicklas\SalesWatcher\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Nicklas\SalesWatcher\Ui\DataProvider\OrderMonitorDataProvider;

class Data extends Action
{
    protected $dataProvider;

    public function __construct(
        Action\Context $context,
        OrderMonitorDataProvider $dataProvider
    ) {
        parent::__construct($context);
        $this->dataProvider = $dataProvider;
    }

    public function execute()
    {
        $statuses = ['holded', 'payment_failed', 'canceled'];
        // Get filtered data
        return $this->dataProvider->getDataAsJson($statuses);
    }
}
