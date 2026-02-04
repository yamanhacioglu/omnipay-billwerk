<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Refund Response
 */
class RefundResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && in_array($this->data['state'], ['refunded', 'processing'])
            && !isset($this->data['error']);
    }

    /**
     * Get refund ID
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get refund state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }
}
