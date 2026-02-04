<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Capture Response
 */
class CaptureResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && $this->data['state'] === 'settled'
            && !isset($this->data['error']);
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
     * Get charge state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->data['state'] ?? null;
    }
}
