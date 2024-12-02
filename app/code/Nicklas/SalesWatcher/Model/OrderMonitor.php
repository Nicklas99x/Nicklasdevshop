<?php

namespace Nicklas\SalesWatcher\Model;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;

class OrderMonitor
{
    protected $orderCollectionFactory;
    protected $logger;
    protected $transportBuilder;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Process failed orders and send email alerts.
     */
    public function process()
    {
        // Fetch failed orders
        $failedOrders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', ['in' => ['canceled', 'payment_review', 'holded']])
            ->load();

        if ($failedOrders->getSize() > 0) {
            $this->sendAlertEmail($failedOrders);
        }
    }

    /**
     * Send alert email for failed orders.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $failedOrders
     */
    private function sendAlertEmail($failedOrders)
    {
        try {
            $recipientEmail = 'nicklas@erpinima.dk';
            $recipientName = 'Nicklas';

            $orderDetails = [];
            foreach ($failedOrders as $order) {
                $orderDetails[] = [
                    'increment_id' => $order->getIncrementId(),
                    'status' => $order->getStatus(),
                ];
            }

            $emailData = [
                'failed_orders' => $failedOrders->getSize(),
                'order_details' => $orderDetails,
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('alert_email_template') // Your template ID
                ->setTemplateOptions(['area' => 'adminhtml', 'store' => 1])
                ->setTemplateVars($emailData)
                ->setFrom(['name' => 'Sales Monitor', 'email' => 'nicklasp24@gmail.com'])
                ->addTo($recipientEmail, $recipientName)
                ->getTransport();

            $transport->sendMessage();
            $this->logger->info('Alert email sent for failed orders.');
        } catch (\Exception $e) {
            $this->logger->error('Error sending alert email: ' . $e->getMessage());
        }
    }
}
