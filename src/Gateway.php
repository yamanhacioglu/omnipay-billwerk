<?php

declare(strict_types=1);

namespace Omnipay\Billwerk;

use Omnipay\Billwerk\Message\PurchaseRequest;
use Omnipay\Billwerk\Message\CompletePurchaseRequest;
use Omnipay\Billwerk\Message\AuthorizeRequest;
use Omnipay\Billwerk\Message\CompleteAuthorizeRequest;
use Omnipay\Billwerk\Message\CaptureRequest;
use Omnipay\Billwerk\Message\RefundRequest;
use Omnipay\Billwerk\Message\VoidRequest;
use Omnipay\Billwerk\Message\CreateCardRequest;
use Omnipay\Billwerk\Message\DeleteCardRequest;
use Omnipay\Billwerk\Message\FetchTransactionRequest;
use Omnipay\Billwerk\Message\CreateCustomerRequest;
use Omnipay\Billwerk\Message\UpdateCustomerRequest;
use Omnipay\Billwerk\Message\FetchCustomerRequest;
use Omnipay\Billwerk\Message\CreateSubscriptionRequest;
use Omnipay\Billwerk\Message\CancelSubscriptionRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Billwerk Gateway
 *
 * This gateway provides integration with the Billwerk (Reepay) payment platform.
 * Billwerk supports recurring billing, subscriptions, and one-time payments.
 *
 * Example:
 *
 * ```php
 * // Create a gateway instance
 * $gateway = Omnipay::create('Billwerk');
 *
 * // Initialize with credentials
 * $gateway->initialize([
 *     'apiKey' => 'your-private-api-key',
 *     'testMode' => true
 * ]);
 *
 * // Create a charge session
 * $response = $gateway->purchase([
 *     'amount' => '10.00',
 *     'currency' => 'EUR',
 *     'description' => 'Order #1234',
 *     'returnUrl' => 'https://example.com/return',
 *     'cancelUrl' => 'https://example.com/cancel'
 * ])->send();
 *
 * if ($response->isRedirect()) {
 *     $response->redirect();
 * }
 * ```
 *
 * @link https://reference.reepay.com/api/
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Billwerk';
    }

    /**
     * Get default parameters
     *
     * @return array<string, mixed>
     */
    public function getDefaultParameters(): array
    {
        return [
            'apiKey' => '',
            'testMode' => false,
        ];
    }

    /**
     * Get API Key
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API Key
     *
     * @param string $value
     * @return $this
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Create a purchase request (one-time charge)
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Complete a purchase
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function completePurchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    /**
     * Authorize a payment
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function authorize(array $options = []): AbstractRequest
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * Complete an authorization
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function completeAuthorize(array $options = []): AbstractRequest
    {
        return $this->createRequest(CompleteAuthorizeRequest::class, $options);
    }

    /**
     * Capture an authorized payment
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function capture(array $options = []): AbstractRequest
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    /**
     * Refund a payment
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function refund(array $options = []): AbstractRequest
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * Void/Cancel a payment
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function void(array $options = []): AbstractRequest
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    /**
     * Create a payment method (card)
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function createCard(array $options = []): AbstractRequest
    {
        return $this->createRequest(CreateCardRequest::class, $options);
    }

    /**
     * Delete a payment method
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function deleteCard(array $options = []): AbstractRequest
    {
        return $this->createRequest(DeleteCardRequest::class, $options);
    }

    /**
     * Fetch transaction details
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function fetchTransaction(array $options = []): AbstractRequest
    {
        return $this->createRequest(FetchTransactionRequest::class, $options);
    }

    /**
     * Create a customer
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function createCustomer(array $options = []): AbstractRequest
    {
        return $this->createRequest(CreateCustomerRequest::class, $options);
    }

    /**
     * Update a customer
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function updateCustomer(array $options = []): AbstractRequest
    {
        return $this->createRequest(UpdateCustomerRequest::class, $options);
    }

    /**
     * Fetch customer details
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function fetchCustomer(array $options = []): AbstractRequest
    {
        return $this->createRequest(FetchCustomerRequest::class, $options);
    }

    /**
     * Create a subscription
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function createSubscription(array $options = []): AbstractRequest
    {
        return $this->createRequest(CreateSubscriptionRequest::class, $options);
    }

    /**
     * Cancel a subscription
     *
     * @param array<string, mixed> $options
     * @return AbstractRequest
     */
    public function cancelSubscription(array $options = []): AbstractRequest
    {
        return $this->createRequest(CancelSubscriptionRequest::class, $options);
    }
}
