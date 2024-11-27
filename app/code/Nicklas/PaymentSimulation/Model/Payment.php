<?php

namespace Nicklas\PaymentSimulation\Model;

use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Payment implements MethodInterface
{
    protected $_code = 'paymentsimulation';
    protected $infoInstance;
    const ACTION_ORDER = 'order';
    const ACTION_AUTHORIZE = 'authorize';
    const ACTION_AUTHORIZE_CAPTURE = 'authorize_capture';
    /**
     * Different payment method checks.
     */
    const CHECK_USE_FOR_COUNTRY = 'country';
    const CHECK_USE_FOR_CURRENCY = 'currency';
    const CHECK_USE_CHECKOUT = 'checkout';
    const CHECK_USE_INTERNAL = 'internal';
    const CHECK_ORDER_TOTAL_MIN_MAX = 'total';
    const CHECK_ZERO_TOTAL = 'zero_total';
    const GROUP_OFFLINE = 'offline';
    protected $scopeConfig;
    protected $logger;

    public function __construct(ScopeConfigInterface $scopeConfig, LoggerInterface $logger)
{
    $this->scopeConfig = $scopeConfig;
    $this->logger = $logger;
}

    /**
     * Retrieve payment method code
     *
     * @return string
     *
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     *
     * @deprecated 100.0.2
     */
    public function getFormBlockType()
    {
        return null; // No custom form block needed.
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     *
     */
    public function getTitle()
    {
        return __(['Mock Payment Method']);
    }

    /**
     * Store id setter
     * @param int $storeId
     * @return void
     */
    public function setStore($storeId)
    {
        return $this; // Store-specific logic not required.
    }

    /**
     * Store id getter
     * @return int
     */
    public function getStore()
    {
        return null; // Store scope is not used.
    }

    /**
     * Check order availability
     *
     * @return bool
     *
     */
    public function canOrder()
    {
        return false; // "Order" action not supported.
    }

    /**
     * Check authorize availability
     *
     * @return bool
     *
     */
    public function canAuthorize()
    {
        return false;
    }

    /**
     * Check capture availability
     *
     * @return bool
     *
     */
    public function canCapture()
    {
        return true; // Allow capturing payments.
    }

    /**
     * Check partial capture availability
     *
     * @return bool
     *
     */
    public function canCapturePartial()
    {
        return false; // Partial capture not supported.
    }

    /**
     * Check whether capture can be performed once and no further capture possible
     *
     * @return bool
     *
     */
    public function canCaptureOnce()
    {
        return true; // Single capture per transaction allowed.
    }

    /**
     * Check refund availability
     *
     * @return bool
     *
     */
    public function canRefund()
    {
        return false; // Refunds are not supported.
    }

    /**
     * Check partial refund availability for invoice
     *
     * @return bool
     *
     */
    public function canRefundPartialPerInvoice()
    {
        return false; // Partial refunds not supported.
    }

    /**
     * Check void availability
     * @return bool
     *
     */
    public function canVoid()
    {
        return true; // Enable for admin area.
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return true; // Enable for internal.
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return true; // Enable for checkout.
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     *
     */
    public function canEdit()
    {
        return false; // Editing is not supported.
    }

    /**
     * Check fetch transaction info availability
     *
     * @return bool
     *
     */
    public function canFetchTransactionInfo()
    {
        return true; // Mock transaction info available.
    }

    /**
     * Fetch transaction info
     *
     * @param InfoInterface $payment
     * @param string $transactionId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function fetchTransactionInfo(InfoInterface $payment, $transactionId)
    {
        return ['transaction_id' => $transactionId, 'status' => 'mocked'];
    }

    /**
     * Retrieve payment system relation flag
     *
     * @return bool
     *
     */
    public function isGateway()
    {
        return false;
    }

    /**
     * Retrieve payment method online/offline flag
     *
     * @return bool
     *
     */
    public function isOffline()
    {
        return false;
    }

    /**
     * Flag if we need to run payment initialize while order place
     *
     * @return bool
     *
     */
    public function isInitializeNeeded()
    {
        return false;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        $allowedCountries = explode(',', $this->getConfigData('specificcountry'));
        if ($this->getConfigData('allowspecific') && !in_array($country, $allowedCountries)) {
            return false;
        }
        return true;
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canUseForCurrency($currencyCode)
    {
        return true; // Available for all currencies.
    }

    /**
     * Retrieve block type for display method information
     *
     * @return string
     *
     * @deprecated 100.0.2
     */
    public function getInfoBlockType()
    {
        return false;
    }

    /**
     * Retrieve payment information model object
     *
     * @return InfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @deprecated 100.0.2
     */
    public function getInfoInstance()
    {
        return $this->infoInstance;
    }

    /**
     * Retrieve payment information model object
     *
     * @param InfoInterface $info
     * @return void
     *
     * @deprecated 100.0.2
     */
    public function setInfoInstance(InfoInterface $infoInstance)
    {
        $this->infoInstance = $infoInstance;
        return $this;
    }

    /**
     * Validate payment method information object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function validate()
    {
        // No custom validation needed for the mock.
        return $this;
    }

    /**
     * Order payment abstract method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['Order operation is not supported.'])
        );
    }

    /**
     * Authorize payment abstract method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['Not supported yet'])
        );
    }

    /**
     * Capture payment abstract method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($payment instanceof \Magento\Payment\Model\Info) {
            $payment->setTransactionId(uniqid());
            $payment->setIsTransactionClosed(1); // Mark transaction as closed
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid payment instance')
            );
        }
        
        return $this;
    }

    /**
     * Refund specified amount for payment
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function refund(InfoInterface $payment, $amount)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['We dont do refunds sucks to be you.'])
        );
    }

    /**
     * Cancel payment abstract method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['cancel operation is not supported.'])
        );
    }

    /**
     * Void payment abstract method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['void operation is not supported.'])
        );
    }

    /**
     * Whether this method can accept or deny payment
     * @return bool
     *
     */
    public function canReviewPayment()
    {
        return false; // Mock payment does not support review.
    }

    /**
     * Attempt to accept a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function acceptPayment(InfoInterface $payment)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['Accept payment operation is not supported.'])
        );
    }

    /**
     * Attempt to deny a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function denyPayment(InfoInterface $payment)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['Deny payment operation is not supported.'])
        );
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|\Magento\Store\Model\Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        $path = "payment/{$this->_code}/{$field}";
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Assign data to info model instance
     *
     * @param DataObject $data
     * @return $this
     *
     */
    public function assignData(DataObject $data)
    {
        $this->getInfoInstance()->setAdditionalInformation(
            'custom_field',
            $data->getData('custom_field') // Example custom field handling
        );
        return $this;
    }

    /**
     * Check whether payment method can be used
     *
     * @param CartInterface|null $quote
     * @return bool
     *
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $this->logger->info('Payment method isAvailable check started.');

        if (!$this->getConfigData('active')) {
            $this->logger->info('Payment method is not active.');
            return false;
        }

        if ($quote instanceof \Magento\Quote\Model\Quote) {
            $total = $quote->getBaseGrandTotal();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid quote object provided.')
            );
        }
        $minTotal = $this->getConfigData('min_order_total');
        $maxTotal = $this->getConfigData('max_order_total');

        if ($minTotal && $total < $minTotal) {
            $this->logger->info('Order total below minimum threshold.');
            return false;
        }

        if ($maxTotal && $total > $maxTotal) {
            $this->logger->info('Order total exceeds maximum threshold.');
            return false;
        }

        $this->logger->info('Payment method is available.');
        return true;
    }

    /**
     * Is active
     *
     * @param int|null $storeId
     * @return bool
     *
     */
    public function isActive($storeId = null)
    {
        return true; // Always active for the mock.
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function initialize($paymentAction, $stateObject)
    {
        throw new \Magento\Framework\Exception\LocalizedException(
            __(['Initialize operation is not supported.'])
        );
    }

    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     *
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('payment_action') ?: MethodInterface::ACTION_AUTHORIZE; // Default to authorize action.
    }
}