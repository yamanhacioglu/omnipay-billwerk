<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && in_array($this->data['state'], ['authorized', 'settled', 'paid'])
            && !isset($this->data['error']);
    }

    /**
     * Is payment pending?
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return isset($this->data['state'])
            && in_array($this->data['state'], ['pending', 'processing']);
    }

    /**
     * Get invoice state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }

    /**
     * Get amount
     *
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->data['amount'] ?? null;
    }

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->data['currency'] ?? null;
    }

    /**
     * Is settled?
     *
     * @return bool
     */
    public function isSettled(): bool
    {
        return $this->data['settled'] ?? false;
    }

    /**
     * Is authorized?
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->data['authorized'] ?? false;
    }
}
