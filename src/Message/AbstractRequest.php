<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\BillwerkClientFactory;
use Billwerk\Sdk\Sdk;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 * Base class for all Billwerk requests
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /**
     * @var Sdk|null
     */
    protected ?Sdk $sdk = null;

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
     * Get Test Mode
     *
     * @return bool
     */
    public function getTestMode(): bool
    {
        return (bool) $this->getParameter('testMode');
    }

    /**
     * Set Test Mode
     *
     * @param bool $value
     * @return $this
     */
    public function setTestMode($value): self
    {
        return $this->setParameter('testMode', $value);
    }

    /**
     * Get Customer Reference
     *
     * @return string|null
     */
    public function getCustomerReference(): ?string
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set Customer Reference
     *
     * @param string $value
     * @return $this
     */
    public function setCustomerReference($value): self
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Get Return URL
     *
     * @return string|null
     */
    public function getReturnUrl(): ?string
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * Set Return URL
     *
     * @param string $value
     * @return $this
     */
    public function setReturnUrl($value): self
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * Get Cancel URL
     *
     * @return string|null
     */
    public function getCancelUrl(): ?string
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * Set Cancel URL
     *
     * @param string $value
     * @return $this
     */
    public function setCancelUrl($value): self
    {
        return $this->setParameter('cancelUrl', $value);
    }

    /**
     * Get Billwerk SDK instance
     *
     * @return Sdk
     */
    protected function getSdk(): Sdk
    {
        if ($this->sdk === null) {
            // Create PSR-17 factories from Guzzle
            $httpClient = $this->httpClient;

            // Use Guzzle as PSR-17 factory
            $psr17Factory = new \GuzzleHttp\Psr7\HttpFactory();

            $clientFactory = new BillwerkClientFactory(
                $httpClient,
                $psr17Factory, // RequestFactory
                $psr17Factory  // StreamFactory
            );

            $this->sdk = new Sdk(
                $clientFactory,
                $this->getApiKey() ?? ''
            );
        }

        return $this->sdk;
    }

    /**
     * Get base endpoint URL
     *
     * @return string
     */
    protected function getEndpoint(): string
    {
        return $this->getTestMode()
            ? 'https://api.test.reepay.com/v1'
            : 'https://api.reepay.com/v1';
    }

    /**
     * Get checkout endpoint URL
     *
     * @return string
     */
    protected function getCheckoutEndpoint(): string
    {
        return $this->getTestMode()
            ? 'https://checkout-api.test.reepay.com/v1'
            : 'https://checkout-api.reepay.com/v1';
    }
}
