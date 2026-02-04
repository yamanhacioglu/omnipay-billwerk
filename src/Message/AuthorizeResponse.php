<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Authorize Response
 */
class AuthorizeResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && $this->data['state'] === 'authorized'
            && !isset($this->data['error']);
    }

    /**
     * Get charge handle
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->data['handle'] ?? null;
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
