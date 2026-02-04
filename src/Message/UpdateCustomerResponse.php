<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Update Customer Response
 */
class UpdateCustomerResponse extends AbstractResponse
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
     * Get customer handle
     *
     * @return string|null
     */
    public function getCustomerReference(): ?string
    {
        return $this->data['handle'] ?? null;
    }
}
