<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

/**
 * Delete Card Response
 */
class DeleteCardResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['state'])
            && $this->data['state'] === 'inactivated'
            && !isset($this->data['error']);
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->data['deleted'] ?? false;
    }
}
