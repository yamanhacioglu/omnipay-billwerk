<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Charge\ChargeCreateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize Request
 *
 * Creates an authorization (charge without immediate settlement)
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('amount', 'currency', 'customerReference');

        $data = [
            'handle' => $this->getTransactionId() ?? uniqid('charge_', true),
            'customer' => $this->getCustomerReference(),
            'amount' => $this->getAmountInteger(),
            'currency' => $this->getCurrency(),
            'settle' => false, // Don't settle immediately (authorization only)
        ];

        if ($this->getDescription()) {
            $data['ordertext'] = $this->getDescription();
        }

        // If card reference is provided
        if ($this->getCardReference()) {
            $data['source'] = $this->getCardReference();
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return AuthorizeResponse
     */
    public function sendData($data): AuthorizeResponse
    {
        try {
            $chargeModel = ChargeCreateModel::fromArray($data);
            $charge = $this->getSdk()->charge()->create($chargeModel);

            $responseData = [
                'handle' => $charge->getHandle(),
                'state' => $charge->getState(),
                'amount' => $charge->getAmount(),
                'currency' => $charge->getCurrency(),
                'authorized' => $charge->getAuthorized(),
                'created' => $charge->getCreated(),
            ];

            return new AuthorizeResponse($this, $responseData);
        } catch (\Exception $e) {
            return new AuthorizeResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Get card reference
     *
     * @return string|null
     */
    public function getCardReference(): ?string
    {
        return $this->getParameter('cardReference');
    }

    /**
     * Set card reference
     *
     * @param string $value
     * @return $this
     */
    public function setCardReference(string $value): self
    {
        return $this->setParameter('cardReference', $value);
    }
}
