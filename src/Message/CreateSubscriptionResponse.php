<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Create Subscription Response
 */
class CreateSubscriptionResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['handle']) && !isset($this->data['error']);
    }

    /**
     * Get subscription handle
     *
     * @return string|null
     */
    public function getSubscriptionReference(): ?string
    {
        return $this->data['handle'] ?? null;
    }

    /**
     * Get transaction reference (alias for subscription reference)
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->getSubscriptionReference();
    }

    /**
     * Get subscription state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }
}
