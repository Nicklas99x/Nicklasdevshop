<?php 

namespace Nicklas\SalesWatcher\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Nicklas\SalesWatcher\Ui\DataProvider\OrderMonitorDataProvider;

class Index extends Action
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
        return $this->dataProvider->getDataAsJson();
    }
}
