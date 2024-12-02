<?php

namespace Nicklas\SalesWatcher\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class MonitorOrders
{
    private $orderCollectionFactory;
    private $logger;
    private $transportBuilder;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
    }

    public function execute()
    {
        $failedOrders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', ['in' => ['canceled', 'payment_review', 'holded']])
            ->load();

        if ($failedOrders->getSize() > 0) {
            $this->sendAlertEmail($failedOrders);
        }

        return $this;
    }

    private function sendAlertEmail($failedOrders)
    {
        $recipientEmail = 'nicklas@erpinima.dk'; // Replace with actual email
        $recipientName = 'Nicklas';
        $emailData = [
            'failed_orders' => $failedOrders->getSize(),
            'order_details' => $failedOrders->getData(),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('alert_email_template') // Use the correct template identifier
            ->setTemplateOptions(['area' => 'adminhtml', 'store' => 1]) // Set the area to adminhtml
            ->setTemplateVars($emailData)
            ->setFrom(['name' => 'Sales Monitor', 'email' => 'nicklasp24@gmail.com'])
            ->addTo($recipientEmail, $recipientName)
            ->getTransport();

        $transport->sendMessage();
        $this->logger->info('Alert email sent for failed orders.');
    }
}
