<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Fetch Transaction Response
 */
class FetchTransactionResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['id']) && !isset($this->data['error']);
    }

    /**
     * Get transaction state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }

    /**
     * Get transaction type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->data['type'] ?? null;
    }
}
