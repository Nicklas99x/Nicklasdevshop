<?php

namespace Nicklas\MockPayment\Controller\Mock;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Raw;
use Psr\Log\LoggerInterface;

class Callback implements HttpGetActionInterface
{
    private $resultFactory;
    private $logger;

    public function __construct(
        ResultFactory $resultFactory,
        LoggerInterface $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        /** @var Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        try {
            if (rand(0, 1)) {
                $this->logger->info('Mock payment succeeded.');
                $result->setContents('Success');
            } else {
                throw new \Exception('Mock payment failed.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $result->setContents('Failure');
        }

        return $result;
    }
}