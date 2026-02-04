<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Purchase Response
 */
class PurchaseResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return false; // Always requires redirect to checkout
    }

    /**
     * Is the response a redirect?
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return isset($this->data['url']) && !isset($this->data['error']);
    }

    /**
     * Get redirect URL
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }

    /**
     * Get session ID
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get session state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }
}
