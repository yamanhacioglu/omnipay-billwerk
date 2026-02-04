<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Void Response
 */
class VoidResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && $this->data['state'] === 'cancelled'
            && !isset($this->data['error']);
    }

    /**
     * Is cancelled?
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->data['cancelled'] ?? false;
    }

    /**
     * Get charge state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }
}
