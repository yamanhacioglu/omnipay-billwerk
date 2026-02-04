<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Create Customer Response
 */
class CreateCustomerResponse extends AbstractResponse
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

    /**
     * Get transaction reference (alias for customer reference)
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->getCustomerReference();
    }
}
